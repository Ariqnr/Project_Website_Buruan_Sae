<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActiveAndPasswordUpdated
{
    /**
     * Pastikan akun yang sedang login berstatus aktif dan sudah mengganti kata sandi sementara.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        // Ambil data terbaru dari database jika atribut belum ter-load atau telah diubah admin
        $freshUser = $user->fresh();
        if ($freshUser !== null) {
            $user = $freshUser;
        }

        if (! $user->is_active) {
            Auth::guard('web')->logout();

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi admin.',
                ]);
        }

        if ($user->must_change_password && ! $request->routeIs('profile.edit', 'password.update', 'logout')) {
            return redirect()
                ->route('profile.edit')
                ->with('warning', 'Demi keamanan, silakan ganti kata sandi sementara Anda terlebih dahulu.');
        }

        return $next($request);
    }
}
