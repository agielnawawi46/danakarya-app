<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\ShuMemberDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ShuController extends Controller
{
    public function index(): View
    {
        $user    = Auth::user();
        $details = ShuMemberDetail::where('user_id', $user->id)
            ->with('distribution')
            ->orderByDesc('created_at')
            ->get();

        $totalShu = $details->sum('total_shu');

        return view('member.shu.index', compact('details', 'totalShu'));
    }
}
