<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display all users with optional search and role filter.
     */
    public function index(Request $request)
    {
        $query = User::with(['admin', 'mitra']);

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

        [$sortBy, $sortDir] = $this->resolveSort($request);

        $users = $query->orderBy($sortBy, $sortDir)
            ->paginate(15)
            ->withQueryString();

        // Transform each user into a flat, UI-ready shape
        $users->getCollection()->transform(function (User $user) {
            return $this->formatUser($user);
        });

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'status', 'sort_by', 'sort_dir']),
        ]);
    }

    /**
     * Create a new user (admin or mitra).
     *
     * Note: For role=mitra, this only creates the User account; the mitra profile
     * will be completed by the user via the "Complete Profile" flow.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $plainPassword = $validated['password'] ?: Str::password(12);

        $user = User::create([
            'email' => $validated['email'],
            'password' => Hash::make($plainPassword),
            'role' => $validated['role'],
        ]);

        if ($validated['role'] === 'admin') {
            Admin::create([
                'id_user' => $user->id_user,
                'nama' => $validated['username'],
                'divisi' => $validated['instansi'],
            ]);
        }

        $response = back()->with('success', 'Pengguna berhasil ditambahkan.');

        // Only flash the generated password when admin did not provide one.
        if (empty($validated['password'])) {
            $response->with('generated_password', $plainPassword);
        }

        return $response;
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

    /**
     * Update an existing user.
     *
     * Designed to match the admin panel form fields: username, email, instansi.
     * Role changes are intentionally not supported here.
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        $validated = $request->validated();

        $user = User::with(['admin', 'mitra'])->findOrFail($id);

        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        if ($user->role === 'admin') {
            $user->admin()->updateOrCreate(
                ['id_user' => $user->id_user],
                [
                    'nama' => $validated['username'],
                    'divisi' => $validated['instansi'],
                ]
            );
        }

        if ($user->role === 'mitra' && $user->mitra) {
            // Optional: allow editing minimal mitra identity fields when profile exists.
            $updates = array_filter([
                'pic' => $validated['username'] ?? null,
                'nama_perusahaan' => $validated['instansi'] ?? null,
            ], fn ($value) => $value !== null);

            if (! empty($updates)) {
                $user->mitra->fill($updates)->save();
            }
        }

        return back()->with('success', 'Pengguna berhasil diperbarui.');
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

    private function resolveSort(Request $request): array
    {
        $allowedSort = ['created_at', 'email', 'role'];

        $sortBy = (string) $request->input('sort_by', 'created_at');
        if (! in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'created_at';
        }

        $sortDir = strtolower((string) $request->input('sort_dir', 'desc'));
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        return [$sortBy, $sortDir];
    }
}
