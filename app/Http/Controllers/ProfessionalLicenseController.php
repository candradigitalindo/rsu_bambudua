<?php

namespace App\Http\Controllers;

use App\Models\ProfessionalLicense;
use App\Models\User;
use Illuminate\Http\Request;

class ProfessionalLicenseController extends Controller
{
    // Profesi yang didukung
    public const PROFESSIONS = [
        'dokter', 'perawat', 'apoteker', 'asisten_apoteker', 'radiografer', 'analis_lab',
    ];

    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $profession = $request->get('profession');
        $licenses = ProfessionalLicense::with(['user.profile'])
            ->when($q !== '', function ($w) use ($q) {
                $w->whereHas('user', function ($wu) use ($q) {
                    $wu->where('name', 'like', "%$q%")
                       ->orWhere('username', 'like', "%$q%")
                       ->orWhere('id_petugas', 'like', "%$q%")
                    ;
                });
            })
            ->when($profession, fn($w) => $w->where('profession', $profession))
            ->orderBy('sip_expiry_date')
            ->paginate(15)
            ->appends($request->query());

        return view('pages.professional_licenses.index', [
            'licenses' => $licenses,
            'professions' => self::PROFESSIONS,
            'q' => $q,
            'selectedProfession' => $profession,
        ]);
    }

    public function create()
    {
        $users = User::orderBy('name')->limit(200)->get(['id','name','username']);
        return view('pages.professional_licenses.form', [
            'license' => new ProfessionalLicense(),
            'users' => $users,
            'professions' => self::PROFESSIONS,
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'profession' => 'required|string|in:'.implode(',', self::PROFESSIONS),
            'sip_number' => 'nullable|string|max:255',
            'sip_expiry_date' => 'required|date',
        ]);

        ProfessionalLicense::create($data);
        return redirect()->route('professional-licenses.index')->with('success', 'Data SIP berhasil dibuat.');
    }

    public function edit($id)
    {
        $license = ProfessionalLicense::with('user')->findOrFail($id);
        $users = User::orderBy('name')->limit(200)->get(['id','name','username']);
        return view('pages.professional_licenses.form', [
            'license' => $license,
            'users' => $users,
            'professions' => self::PROFESSIONS,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, $id)
    {
        $license = ProfessionalLicense::findOrFail($id);
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'profession' => 'required|string|in:'.implode(',', self::PROFESSIONS),
            'sip_number' => 'nullable|string|max:255',
            'sip_expiry_date' => 'required|date',
        ]);
        $license->update($data);
        return redirect()->route('professional-licenses.index')->with('success', 'Data SIP berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $license = ProfessionalLicense::findOrFail($id);
        $license->delete();
        return redirect()->route('professional-licenses.index')->with('success', 'Data SIP berhasil dihapus.');
    }
}