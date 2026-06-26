<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class MemberController extends Controller
{
    public function __construct(private readonly AuditService $auditService) {}

    public function index(Request $request): View
    {
        $orgId = Auth::user()->organization_id;
        $query = User::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->whereHas('roles', fn($q) => $q->where('name', '!=', 'superadmin'));

        if ($request->filled('search')) {
            $query->where(fn($q) => $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('employee_id', 'like', '%' . $request->search . '%'));
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->role));
        }

        $members = $query->with('roles')->paginate(20);
        $roles   = ['admin', 'pengurus', 'pengawas', 'anggota'];

        $allMembersForAutocomplete = User::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->whereHas('roles', fn($q) => $q->where('name', '!=', 'superadmin'))
            ->get(['name', 'email', 'employee_id']);

        return view('admin.members.index', compact('members', 'roles', 'allMembersForAutocomplete'));
    }

    public function create(): View
    {
        return view('admin.members.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $orgId = Auth::user()->organization_id;
        $org   = Auth::user()->organization;

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'unique:users,email'],
            'password'    => ['required', 'min:8'],
            'role'        => ['required', 'in:pengurus,pengawas,anggota'],
            'employee_id' => ['nullable', 'string', 'max:50'],
            'department'  => ['nullable', 'string', 'max:100'],
            'salary'      => ['nullable', 'numeric', 'min:0'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'join_date'   => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($validated, $orgId, $org) {
            $user = User::create([
                ...$validated,
                'organization_id' => $orgId,
                'password'        => bcrypt($validated['password']),
                'status'          => 'active',
            ]);

            setPermissionsTeamId($orgId);
            $user->assignRole($validated['role']);

            // Auto-create simpanan pokok for new anggota
            if ($validated['role'] === 'anggota' && $org->simpanan_pokok > 0) {
                Deposit::create([
                    'organization_id' => $orgId,
                    'user_id'         => $user->id,
                    'type'            => 'pokok',
                    'amount'          => $org->simpanan_pokok,
                    'status'          => 'pending',
                    'transaction_type'=> 'credit',
                    'notes'           => 'Simpanan Pokok Awal',
                    'processed_by'    => Auth::id(),
                ]);
            }

            $this->auditService->log('created_member', "Anggota baru: {$user->name} ({$validated['role']})", 'User', $user->id);
        });

        return redirect()->route('admin.members.index')
            ->with('success', 'Anggota berhasil ditambahkan!');
    }

    public function edit(User $user): View
    {
        return view('admin.members.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', "unique:users,email,{$user->id}"],
            'employee_id' => ['nullable', 'string', 'max:50'],
            'department'  => ['nullable', 'string', 'max:100'],
            'salary'      => ['nullable', 'numeric', 'min:0'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'status'      => ['required', 'in:active,inactive,suspended'],
            'password'    => ['nullable', 'string', 'min:8'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return back()->with('success', 'Data anggota berhasil diperbarui!');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->update(['status' => 'inactive']);
        return redirect()->route('admin.members.index')
            ->with('success', 'Anggota dinonaktifkan.');
    }

    public function downloadTemplate(): Response
    {
        $csv  = "employee_id,name,email,department,salary,phone,join_date,password\n";
        $csv .= "EMP-001,Nama Karyawan,email@perusahaan.com,Departemen,5000000,08123456789,2024-01-01,Password123!\n";

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template-import-anggota.csv"',
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt']]);

        $orgId = Auth::user()->organization_id;
        $org   = Auth::user()->organization;
        $file  = $request->file('file');
        $rows  = array_map('str_getcsv', file($file->getPathname()));
        $headers = array_shift($rows); // Remove header row

        $success = 0;
        $errors  = [];

        DB::transaction(function () use ($rows, $headers, $orgId, $org, &$success, &$errors) {
            foreach ($rows as $i => $row) {
                try {
                    $data = array_combine($headers, $row);

                    if (User::where('email', $data['email'])->exists()) {
                        $errors[] = "Baris " . ($i + 2) . ": Email {$data['email']} sudah ada.";
                        continue;
                    }

                    $user = User::create([
                        'organization_id' => $orgId,
                        'employee_id'     => $data['employee_id'] ?? null,
                        'name'            => $data['name'],
                        'email'           => $data['email'],
                        'password'        => bcrypt($data['password'] ?? 'Password@123'),
                        'department'      => $data['department'] ?? null,
                        'salary'          => $data['salary'] ?? null,
                        'phone'           => $data['phone'] ?? null,
                        'join_date'       => $data['join_date'] ?? null,
                        'status'          => 'active',
                    ]);

                    setPermissionsTeamId($orgId);
                    $user->assignRole('anggota');

                    if ($org->simpanan_pokok > 0) {
                        Deposit::create([
                            'organization_id' => $orgId,
                            'user_id'         => $user->id,
                            'type'            => 'pokok',
                            'amount'          => $org->simpanan_pokok,
                            'status'          => 'pending',
                            'transaction_type'=> 'credit',
                            'notes'           => 'Simpanan Pokok Awal (Import)',
                        ]);
                    }

                    $success++;
                } catch (\Throwable $e) {
                    $errors[] = "Baris " . ($i + 2) . ": " . $e->getMessage();
                }
            }
        });

        $msg = "{$success} anggota berhasil diimport.";
        if (!empty($errors)) {
            $msg .= " " . count($errors) . " gagal: " . implode('; ', array_slice($errors, 0, 3));
        }

        return redirect()->route('admin.members.index')->with('success', $msg);
    }
}
