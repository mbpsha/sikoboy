<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Admin;
use App\Models\User;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
            $status = (string) $request->input('status');

            match ($status) {
                'aktif' => $query->where(function ($q) {
                        $q->where('role', 'admin')
                        ->orWhere(function ($mitra) {
                            $mitra->where('role', 'mitra')
                            ->where('status_verifikasi', 'disetujui');
                        });
                    }),
                'menunggu_verifikasi' => $query->where('role', 'mitra')->where('status_verifikasi', 'pending'),
                'ditolak' => $query->where('role', 'mitra')->where('status_verifikasi', 'ditolak'),
                default => null,
            };
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

        // Debug log: number of users found
        try {
            Log::info('Admin\UserController@index - users total', ['total' => $users->total()]);
        } catch (\Throwable $e) {
            // ignore logging failures
        }

        // Transform each user into a flat, UI-ready shape
        $users->getCollection()->transform(function (User $user) {
            return $this->formatUser($user);
        });

        return Inertia::render('Admin/Users', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'status', 'sort_by', 'sort_dir']),
        ]);
    }

    /**
     * Create a new user: either mitra (with full mitras row) or admin (with admins row).
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $plainPassword = $validated['password'] ?: Str::password(12);

        DB::transaction(function () use ($validated, $plainPassword): void {
            $user = User::create([
                'email' => $validated['email'],
                'password' => Hash::make($plainPassword),
                'role' => $validated['role'],
                'status_verifikasi' => 'disetujui',
            ]);

            if ($validated['role'] === 'admin') {
                Admin::create([
                    'id_user' => $user->id_user,
                    'nama' => $validated['username'],
                    'divisi' => $validated['instansi'] ?? '',
                ]);
            }

            if ($validated['role'] === 'mitra') {
                Mitra::create([
                    'id_user' => $user->id_user,
                    'nama_perusahaan' => $validated['nama_perusahaan'],
                    'pic' => $validated['pic'],
                    'no_handphone' => $validated['no_handphone'],
                    'alamat' => $validated['alamat'],
                ]);
            }
        });

        $response = back()->with('success', 'Pengguna berhasil ditambahkan.');

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

        if (!empty($validated['password'])) {
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
            ], fn($value) => $value !== null);

            if (!empty($updates)) {
                $user->mitra->fill($updates)->save();
            }
        }

        return back()->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Verify a mitra account so it can access the system.
     */
    public function verifyMitra(int $id)
    {
        $user = User::query()
            ->where('id_user', $id)
            ->where('role', 'mitra')
            ->firstOrFail();

        if ($user->status_verifikasi === 'disetujui') {
            return back()->with('success', 'Akun mitra sudah terverifikasi.');
        }

        $user->update(['status_verifikasi' => 'disetujui']);

        return back()->with('success', 'Akun mitra berhasil diverifikasi.');
    }

    /**
     * Remove the specified user and related records from storage.
     */
    public function destroy(int $id)
    {
        $user = User::with(['admin', 'mitra.kerjasama.periodes', 'mitra.kerjasama.dokumen', 'mitra.kerjasama.riwayatStatus'])->where('id_user', $id)->firstOrFail();

        // Prevent accidental deletion of the currently authenticated admin
        if (auth()->check() && auth()->id() === $user->id_user) {
            return back()->with('error', 'Anda tidak dapat menghapus akun yang sedang digunakan.');
        }

        DB::transaction(function () use ($user) {
            // Delete admin profile if exists
            if ($user->admin) {
                $user->admin->delete();
            }

            // Delete mitra and their kerjasama, periodes, dokumen, riwayatStatus if exists
            if ($user->mitra) {
                foreach ($user->mitra->kerjasama as $k) {
                    // delete related dokumen, periodes and riwayatStatus
                    $k->dokumen()->delete();
                    $k->periodes()->delete();
                    $k->riwayatStatus()->delete();
                    $k->delete();
                }

                $user->mitra->delete();
            }

            // Finally delete the user
            $user->delete();
        });

        return back()->with('success', 'Pengguna berhasil dihapus.');
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

        $statusLabel = 'aktif';
        if ($user->role === 'mitra') {
            $statusLabel = match ($user->status_verifikasi) {
                'pending' => 'menunggu_verifikasi',
                'ditolak' => 'ditolak',
                default => 'aktif',
            };
        }

        $base = [
            'id' => $user->id_user,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $statusLabel,
            'status_verifikasi' => $user->status_verifikasi,
            'can_verify' => $user->role === 'mitra' && $user->status_verifikasi !== 'disetujui',
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
                ->map(fn($k) => [
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
        if (!in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'created_at';
        }

        $sortDir = strtolower((string) $request->input('sort_dir', 'desc'));
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        return [$sortBy, $sortDir];
    }
}
