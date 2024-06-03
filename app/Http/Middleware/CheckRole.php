<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = User::find(Auth::user()->id);
            if ($user->hasRole('adminprov')) { // Assuming 'isAdmin' is a method that checks if the user is an admin
                // return redirect('/adminprov');
                return redirect('/report/kab');
            } else if ($user->hasRole('adminkab')) {
                // return redirect('/adminkab');
                return redirect('/report/kab');
            } else {
                return redirect('/petugas');
            }
        }

        return $next($request);
    }
}
