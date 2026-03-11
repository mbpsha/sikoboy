<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'total_partners' => User::where('role', 'mitra')->count(),
            'verified_partners' => User::where('role', 'mitra')
                ->whereNotNull('email_verified_at')
                ->count(),
            'active_today' => ActivityLog::whereDate('created_at', today())
                ->distinct('user_id')
                ->count('user_id'),
            'recent_registrations' => User::where('role', 'mitra')
                ->with('mitra')
                ->latest('created_at')
                ->take(5)
                ->get()
        ];

        return Inertia::render('Admin/Dashboard', ['stats' => $stats]);
    }

    /**
     * Display list of partners.
     */
    public function partners(Request $request)
    {
        $query = User::where('role', 'mitra')->with('mitra');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhereHas('mitra', function($q) use ($search) {
                      $q->where('nama_perusahaan', 'like', "%{$search}%")
                        ->orWhere('pic', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status === 'unverified') {
                $query->whereNull('email_verified_at');
            } elseif ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $partners = $query->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('Admin/Partners/Index', [
            'partners' => $partners,
            'filters' => $request->only(['search', 'status'])
        ]);
    }

    /**
     * Toggle partner active status.
     */
    public function togglePartnerStatus($id)
    {
        $user = User::where('role', 'mitra')->findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', 'Status partner berhasil diperbarui.');
    }

    /**
     * Show partner detail.
     */
    public function showPartner($id)
    {
        $partner = User::where('role', 'mitra')
            ->with('mitra')
            ->findOrFail($id);

        return Inertia::render('Admin/Partners/Show', [
            'partner' => $partner
        ]);
    }
}
