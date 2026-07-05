<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PayrollService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function __construct(private readonly PayrollService $payrollService) {}

    public function index(Request $request): View
    {
        $org   = Auth::user()->organization;
        $month = (int) ($request->month ?? now()->month);
        $year  = (int) ($request->year  ?? now()->year);

        $billing = $this->payrollService->generateBillingData($org, $month, $year);

        $totalSimpananPokok = array_sum(array_column($billing, 'simpanan_pokok'));
        $totalSimpananWajib = array_sum(array_column($billing, 'simpanan_wajib'));
        $totalAngsuran      = array_sum(array_column($billing, 'angsuran'));
        $totalPotongan      = array_sum(array_column($billing, 'total'));

        return view('admin.payroll.index', compact(
            'org', 'billing', 'month', 'year',
            'totalSimpananPokok', 'totalSimpananWajib', 'totalAngsuran', 'totalPotongan'
        ));
    }

    public function export(Request $request): Response
    {
        $org   = Auth::user()->organization;
        $month = (int) ($request->month ?? now()->month);
        $year  = (int) ($request->year  ?? now()->year);
        $format = $request->query('format', 'csv');

        $billing = $this->payrollService->generateBillingData($org, $month, $year);
        
        $monthNames = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
            5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
            9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
        ];

        if ($format === 'pdf') {
            $totalSimpananPokok = array_sum(array_column($billing, 'simpanan_pokok'));
            $totalSimpananWajib = array_sum(array_column($billing, 'simpanan_wajib'));
            $totalAngsuran      = array_sum(array_column($billing, 'angsuran'));
            $totalPotongan      = array_sum(array_column($billing, 'total'));

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.payroll.pdf', compact('org', 'billing', 'month', 'year', 'monthNames', 'totalSimpananPokok', 'totalSimpananWajib', 'totalAngsuran', 'totalPotongan'));
            return $pdf->download("billing-payroll-{$monthNames[$month]}-{$year}.pdf");
        }

        $csv     = $this->payrollService->generateCsvExport($billing, $month, $year);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"billing-payroll-{$monthNames[$month]}-{$year}.csv\"",
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt']]);

        $org   = Auth::user()->organization;

        if (now()->day !== (int) $org->payroll_date) {
            return back()->with('error', "Import payroll bulan ini hanya bisa dilakukan pada tanggal {$org->payroll_date}.");
        }

        $month = (int) ($request->month ?? now()->month);
        $year  = (int) ($request->year  ?? now()->year);
        $file  = $request->file('file');

        $rows    = array_map('str_getcsv', file($file->getPathname()));
        $headers = array_shift($rows);

        $data = [];
        foreach ($rows as $row) {
            if (count($row) >= count($headers)) {
                $data[] = array_combine($headers, array_slice($row, 0, count($headers)));
            }
        }

        $filePath = $request->file('file')->store("payroll/{$org->id}", 'local');

        $import = $this->payrollService->processImportFile(
            $org, $data, $month, $year, Auth::id(), $filePath
        );

        return redirect()->route('admin.payroll.index', compact('month', 'year'))
            ->with('success', "Payroll berhasil diimport: {$import->success_count} berhasil, {$import->failed_count} gagal.");
    }
}
