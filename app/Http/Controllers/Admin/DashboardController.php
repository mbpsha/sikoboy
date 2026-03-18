<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            'verified_partners' => 0,
            'active_today' => 0,
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

        $partners = $query->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('Admin/Partners/Index', [
            'partners' => $partners,
            'filters' => $request->only(['search'])
        ]);
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
