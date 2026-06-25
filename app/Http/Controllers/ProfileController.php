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
    // ═══════════════════════════════════════════════
    //  READ
    // ═══════════════════════════════════════════════

    public function show()
    {
        return view('profile.show', ['user' => Auth::user()]);
    }

    // ═══════════════════════════════════════════════
    //  UPDATE — nama + email
    //  Form mengirim first_name + last_name (sesuai Figma)
    //  Digabung jadi kolom `name` di DB
    // ═══════════════════════════════════════════════

    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ], [
            'first_name.required' => 'Nama depan wajib diisi.',
            'last_name.required'  => 'Nama belakang wajib diisi.',
            'email.required'      => 'Email wajib diisi.',
            'email.unique'        => 'Email sudah digunakan akun lain.',
        ]);

        $user->update([
            'name'  => trim($request->first_name . ' ' . $request->last_name),
            'email' => $request->email,
        ]);

        return back()->with('success_info', 'Informasi profil berhasil diperbarui.');
    }

    // ═══════════════════════════════════════════════
    //  UPDATE — password
    // ═══════════════════════════════════════════════

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

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update(['password' => $request->password]);

        return back()->with('success_password', 'Password berhasil diperbarui.');
    }

    // ═══════════════════════════════════════════════
    //  UPDATE — avatar/foto profil
    // ═══════════════════════════════════════════════

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'avatar.required' => 'Pilih foto terlebih dahulu.',
            'avatar.image'    => 'File harus berupa gambar.',
            'avatar.mimes'    => 'Format: jpeg, png, jpg, webp.',
            'avatar.max'      => 'Ukuran maks. 2MB.',
        ]);

        $user = Auth::user();

        // Hapus avatar lama (hanya kalau path lokal, bukan URL Google)
        if ($user->avatar
            && ! str_starts_with($user->avatar, 'http')
            && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return back()->with('success_info', 'Foto profil berhasil diperbarui.');
    }

    // ═══════════════════════════════════════════════
    //  DELETE — hapus akun sendiri
    // ═══════════════════════════════════════════════

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

        if ($user->avatar
            && ! str_starts_with($user->avatar, 'http')
            && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Akun berhasil dihapus.');
    }

    // ═══════════════════════════════════════════════
    //  ADMIN — daftar semua user
    // ═══════════════════════════════════════════════

    public function index()
    {
        $this->authorizeAdmin();
        $users = User::latest()->paginate(10);
        return view('profile.index', compact('users'));
    }

    // ═══════════════════════════════════════════════
    //  ADMIN — hapus user tertentu
    // ═══════════════════════════════════════════════

    public function adminDestroy(User $user)
    {
        $this->authorizeAdmin();

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri dari sini.');
        }

        if ($user->avatar
            && ! str_starts_with($user->avatar, 'http')
            && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();
        return back()->with('success', 'User ' . $user->name . ' berhasil dihapus.');
    }

    // ═══════════════════════════════════════════════
    //  HELPER
    // ═══════════════════════════════════════════════

    private function authorizeAdmin(): void
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }
    }
}
