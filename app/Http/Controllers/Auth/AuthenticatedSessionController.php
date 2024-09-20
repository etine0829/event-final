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
        
        // Authenticate the user
        $request->authenticate();

        // Regenerate the session to prevent session fixation attacks
        $request->session()->regenerate();

        // Check if user is authenticated
        if (Auth::check()) {
            // Redirect based on user role
            // $user = Auth::user();

            // if ($user->hasRole('admin')) {
            //     return redirect()->intended(route('admin.dashboard'))->with('success', 'Successfully logged in as Admin');
            // } 
            if (Auth::user()->hasRole('admin')) 
            {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Successful Login');
            }
        }

        // If no role matches, redirect back to login (fallback)
        return redirect()->intended('login')->withErrors(['error' => 'Invalid credentials or role not assigned']);

        
    }

    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();

    //     $request->session()->regenerate();

    //     return redirect()->intended(route('dashboard', absolute: false));
    // }

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
