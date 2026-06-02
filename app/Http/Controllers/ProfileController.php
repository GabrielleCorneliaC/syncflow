<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    // ─────────────────────────────────────────────
    //  READ - Tampilkan halaman profil
    // ─────────────────────────────────────────────

    public function show()
    {
        return view('profile.show', ['user' => Auth::user()]);
    }

    // ─────────────────────────────────────────────
    //  UPDATE - Perbarui nama & email
    // ─────────────────────────────────────────────

    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique'   => 'Email sudah digunakan akun lain.',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Informasi profil berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────
    //  UPDATE - Ganti password
    // ─────────────────────────────────────────────

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
            'password.min'              => 'Password minimal 8 karakter.',
        ]);

        // Cek apakah password lama benar
        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update(['password' => $request->password]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────
    //  UPDATE - Upload foto profil (avatar)
    // ─────────────────────────────────────────────

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'avatar.required' => 'Pilih foto terlebih dahulu.',
            'avatar.image'    => 'File harus berupa gambar.',
            'avatar.mimes'    => 'Format gambar harus jpeg, png, jpg, atau webp.',
            'avatar.max'      => 'Ukuran foto maksimal 2MB.',
        ]);

        $user = Auth::user();

        // Hapus avatar lama jika ada
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Simpan avatar baru ke storage/app/public/avatars/
        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────
    //  DELETE - Hapus akun sendiri
    // ─────────────────────────────────────────────

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required'],
        ], [
            'password.required' => 'Password wajib diisi untuk konfirmasi.',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password tidak sesuai.']);
        }

        // Hapus avatar jika ada
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Akun kamu berhasil dihapus.');
    }

    // ─────────────────────────────────────────────
    //  [ADMIN ONLY] - Daftar semua user
    // ─────────────────────────────────────────────

    public function index()
    {
        $this->authorizeAdmin();
        $users = User::latest()->paginate(10);
        return view('profile.index', compact('users'));
    }

    // ─────────────────────────────────────────────
    //  [ADMIN ONLY] - Hapus user tertentu
    // ─────────────────────────────────────────────

    public function adminDestroy(User $user)
    {
        $this->authorizeAdmin();

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri dari sini.');
        }

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return back()->with('success', 'User ' . $user->name . ' berhasil dihapus.');
    }

    // ─────────────────────────────────────────────
    //  HELPER
    // ─────────────────────────────────────────────

    private function authorizeAdmin(): void
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Akses ditolak. Hanya admin yang diizinkan.');
        }
    }
}
