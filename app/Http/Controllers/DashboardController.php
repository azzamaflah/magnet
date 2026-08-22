<?php

namespace App\Http\Controllers;

use App\Services\DashboardAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardAnalyticsService $analyticsService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $selectedYear = $request->input('year', 'all');
            $data = $this->analyticsService->getAdminDashboardData($selectedYear);
            return view('dashboard', $data);
        }

        if ($user->role === 'user') {
            $data = $this->analyticsService->getUserDashboardData($user);
            return view('dashboard', $data);
        }

        Auth::logout();
        return redirect()->route('login');
    }
}