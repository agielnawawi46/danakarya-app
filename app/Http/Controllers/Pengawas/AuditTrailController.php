<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class AuditTrailController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::with('user')
            ->where('organization_id', auth()->user()->organization_id)
            ->latest('created_at');

        if ($request->filled('action')) $query->where('action', $request->action);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('from')) $query->where('created_at', '>=', $request->from);
        if ($request->filled('to')) $query->where('created_at', '<=', $request->to . ' 23:59:59');

        $logs    = $query->paginate(50);
        $actions = AuditLog::where('organization_id', auth()->user()->organization_id)
            ->distinct()->pluck('action');

        return view('pengawas.audit-trail.index', compact('logs', 'actions'));
    }

    public function export(Request $request)
    {
        $query = AuditLog::with('user')
            ->where('organization_id', auth()->user()->organization_id)
            ->latest('created_at');

        if ($request->filled('action')) $query->where('action', $request->action);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('from')) $query->where('created_at', '>=', $request->from);
        if ($request->filled('to')) $query->where('created_at', '<=', $request->to . ' 23:59:59');

        $logs = $query->get();
        $pdf = Pdf::loadView('pengawas.audit-trail.pdf.index', compact('logs'));
        return $pdf->download('audit_trail.pdf');
    }
}
