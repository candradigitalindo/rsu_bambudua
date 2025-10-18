<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\User;
use App\Repositories\PenggunaRepository;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Carbon;

class PenggunaController extends Controller
{
    public $penggunaRepository;
    public function __construct(PenggunaRepository $penggunaRepository)
    {
        $this->penggunaRepository = $penggunaRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = $this->penggunaRepository->index();
        return view('pages.pengguna.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clinics = $this->penggunaRepository->getClinics();
        $spesialis = $this->penggunaRepository->create();
        return view('pages.pengguna.create', compact('spesialis', 'clinics'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string',
            'username'  => 'required|string|unique:users',
            'role'      => 'required|string',
            'password'  => 'required|min:8',
            'spesialis' => 'required|string',
            'sip_file'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'str_file'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ], [
            'name.required'         => 'Kolom masih kosong',
            'username.required'     => 'Kolom masih kosong',
            'username.unique'       => 'Username ' . $request->username . ' sudah terdaftar',
            'role.required'         => 'Pilih Hak Akses Pengguna',
            'password.required'     => 'Kolom masih kosong',
            'password.min'          => 'Minimal Password 8 karakter',
            'spesialis.required'    => 'Pilih Spesialis'
        ]);

        $pengguna = $this->penggunaRepository->store($request);
        if ($pengguna) {
            Alert::success('Berhasil', 'Data Pengguna Tersimpan!');
        } else {
            Alert::error('Error', 'Data Pengguna Gagal Tersimpan, silahkan coba secara berkala atau hubungi Developer');
        }
        return redirect()->route('pengguna.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = $this->penggunaRepository->edit($id);
        $clinics = $this->penggunaRepository->getClinics();
        return view('pages.pengguna.edit', compact('data', 'clinics'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'      => 'required|string',
            'username'  => 'required|string|unique:users,username,' . $id,
            'role'      => 'required|string',
            'password'  => 'nullable|min:8',
            'spesialis' => 'required|string',
            'sip_file'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'str_file'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ], [
            'name.required'         => 'Kolom masih kosong',
            'username.required'     => 'Kolom masih kosong',
            'username.unique'       => 'Username ' . $request->username . ' sudah terdaftar',
            'role.required'         => 'Pilih Hak Akses Pengguna',
            'password.min'          => 'Minimal Password 8 karakter',
            'spesialis.required'    => 'Pilih Spesialis'
        ]);

        $user = $this->penggunaRepository->update($request, $id);
        if ($user) {
            Alert::success('Berhasil', 'Data Pengguna terupdate!');
        } else {
            Alert::error('Error', 'Data Pengguna Gagal terupdate, silahkan coba secara berkala atau hubungi Developer');
        }
        return redirect()->route('pengguna.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = $this->penggunaRepository->destroy($id);
        Alert::info('Berhasil', 'Data Pengguna ' . $user->name . ' berhasil dihapus!');
        return back();
    }

    public function aturGaji(User $user)
    {
        // Eager load relasi salary
        $user->load('salary');
        return view('pages.pengguna.gaji', compact('user'));
    }

    public function simpanGaji(Request $request, User $user)
    {
        $request->validate([
            'base_salary' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ], [
            'base_salary.required' => 'Gaji pokok harus diisi.',
            'base_salary.numeric' => 'Gaji pokok harus berupa angka.',
        ]);

        Salary::updateOrCreate(
            ['user_id' => $user->id],
            ['base_salary' => $request->base_salary, 'notes' => $request->notes]
        );

        Alert::success('Berhasil', 'Gaji pokok untuk ' . $user->name . ' berhasil disimpan.');
        return redirect()->route('pengguna.index');
    }

    // Aktifitas Pengguna - index
    public function activityIndex(Request $request)
    {
        $query = \App\Models\ActivityLog::with('user')
            ->latest();

        if ($request->filled('user')) {
            $query->where('user_id', $request->integer('user'));
        }
        if ($request->filled('method')) {
            $query->where('method', strtoupper($request->string('method')));
        }
        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('subject', 'like', "%{$q}%")
                    ->orWhere('url', 'like', "%{$q}%")
                    ->orWhere('route_name', 'like', "%{$q}%")
                    ->orWhere('ip', 'like', "%{$q}%");
            });
        }
        // Default: tampilkan aktivitas HARI INI saja ketika filter tanggal tidak diisi
        $from = $request->input('date_from');
        $to   = $request->input('date_to');
        if (empty($from) && empty($to)) {
            $from = $to = now()->toDateString();
        }
        if (!empty($from)) {
            $query->whereDate('created_at', '>=', $from);
        }
        if (!empty($to)) {
            $query->whereDate('created_at', '<=', $to);
        }

        $logs = $query->paginate(20)->withQueryString();
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('pages.pengguna.activity', compact('logs', 'users'));
    }

    // Aktifitas Pengguna - show detail
    public function activityShow(\App\Models\ActivityLog $log)
    {
        $log->load('user');
        return view('pages.pengguna.activity_show', compact('log'));
    }
}
