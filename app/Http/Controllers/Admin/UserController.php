<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display all users with optional search and role filter.
     */
    public function index(Request $request)
    {
        $query = User::with(['admin', 'mitra'])
            ->orderBy('created_at', 'desc');

        // Role filter
        if ($request->filled('role') && in_array($request->role, ['admin', 'mitra'])) {
            $query->where('role', $request->role);
        }

        // Status filter (currently all users are active; reserved for future)
        if ($request->filled('status')) {
            // placeholder – extend when a status column is added to users
        }

        // Keyword search: email, admin.nama/divisi, mitra.nama_perusahaan/pic
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhereHas('admin', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%")
                            ->orWhere('divisi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('mitra', function ($q) use ($search) {
                        $q->where('nama_perusahaan', 'like', "%{$search}%")
                            ->orWhere('pic', 'like', "%{$search}%");
                    });
            });
        }

        $users = $query->paginate(15)->withQueryString();

        // Transform each user into a flat, UI-ready shape
        $users->getCollection()->transform(function (User $user) {
            return $this->formatUser($user);
        });

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'status']),
        ]);
    }

    /**
     * Show the full details of a single user.
     */
    public function show(int $id)
    {
        $user = User::with(['admin', 'mitra.kerjasama.latestPeriode'])->findOrFail($id);

        return Inertia::render('Admin/Users/Show', [
            'user' => $this->formatUser($user, detail: true),
        ]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Build a UI-ready array for a User model.
     */
    private function formatUser(User $user, bool $detail = false): array
    {
        $instansi = match ($user->role) {
            'admin' => $user->admin?->divisi,
            'mitra' => $user->mitra?->nama_perusahaan,
            default => null,
        };

        $base = [
            'id' => $user->id_user,
            'email' => $user->email,
            'role' => $user->role,
            'status' => 'aktif',
            'instansi' => $instansi,
            'tanggal_daftar' => $user->created_at?->format('d/m/Y'),
            'display_name' => $user->display_name,
            'admin' => $user->admin ? [
                'nama' => $user->admin->nama,
                'divisi' => $user->admin->divisi,
            ] : null,
            'mitra' => $user->mitra ? [
                'nama_perusahaan' => $user->mitra->nama_perusahaan,
                'pic' => $user->mitra->pic,
                'no_handphone' => $user->mitra->no_handphone,
                'alamat' => $user->mitra->alamat,
            ] : null,
        ];

        if ($detail && $user->role === 'mitra' && $user->mitra) {
            $base['kerjasama'] = $user->mitra->kerjasama
                ->where('is_finalized', true)
                ->map(fn ($k) => [
                    'id_kerjasama' => $k->id_kerjasama,
                    'judul' => $k->judul,
                    'jenis_kerjasama' => $k->jenis_kerjasama,
                    'status_label' => $k->status_label,
                    'tanggal_mulai' => $k->latestPeriode?->tanggal_mulai,
                    'tanggal_berakhir' => $k->latestPeriode?->tanggal_berakhir,
                ])
                ->values();
        }

        return $base;
    }
}
