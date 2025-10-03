<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Use the underlying Illuminate\Http\Request for session
        request()->session()->regenerate();

            $url = "/";

            if (Auth::user()->role === "admin") {
                $url = "admin/dashboard";
            } else if(Auth::user()->role === "dosen") {
                $url = "dosen/dashboard";
            } else if(Auth::user()->role === "kaprodi") {
                $url = "kaprodi/dashboard";
            }
            else if(Auth::user()->role === "asesor") {
                $url = "asesor/dashboard";
            }
            else if(Auth::user()->role === "p4pm") {
                $url = "p4pm/dashboard";
            }

        return redirect()->intended($url);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
