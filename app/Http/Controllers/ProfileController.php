<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validated();

        // Upload foto profil baru
        if ($request->hasFile('foto_profil')) {

            // Hapus foto profil lama jika ada
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $foto = $request->file('foto_profil')->store(
                'foto-profil',
                'public'
            );

            $data['foto_profil'] = $foto;
        }

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        DB::transaction(function () use ($user, $request) {

            // Ambil semua laporan milik user/pelapor
            $reports = Report::where('user_id', $user->id)->get();

            foreach ($reports as $report) {

                // Hapus foto laporan jika ada
                if ($report->foto) {
                    Storage::disk('public')->delete($report->foto);
                }

                // Hapus riwayat/status laporan jika relasi tersedia
                if (method_exists($report, 'statuses')) {
                    $report->statuses()->delete();
                }

                // Hapus laporan
                $report->delete();
            }

            // Hapus foto profil user jika ada
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            // Logout dulu
            Auth::logout();

            // Hapus akun user
            $user->delete();

            // Hapus session login
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        });

        return Redirect::to('/')
            ->with('account_deleted', 'Akun tidak ditemukan karena akun dan seluruh data terkait sudah berhasil dihapus dari sistem.');
    }
}