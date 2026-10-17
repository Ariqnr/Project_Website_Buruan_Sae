<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Batasi akses route hanya untuk role tertentu.
     *
     * Contoh pemakaian: ->middleware('role:admin') atau ->middleware('role:admin,pengelola').
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->guest(route('login'));
        }

        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Maaf, Anda tidak memiliki izin untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
