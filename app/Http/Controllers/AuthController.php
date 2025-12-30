<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect based on Role
            return $this->redirectBasedOnRole(Auth::user()->role);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    private function redirectBasedOnRole($role)
    {
        switch ($role) {
            case 'Project Manager':
            case 'Technical Manager':
                return redirect()->intended('/dashboard'); // Overall access
            
            case 'Finance':
                return redirect()->intended('/finance-overview'); // Finance access
            
            case 'Authority':
                // Assuming you might create a specific milestones view later
                // For now, sending to projects so they can see timelines
                return redirect()->intended('/projects'); 
            
            case 'Supply Chain':
                // Redirect to a specialized inventory view
                return redirect()->intended('/inventory'); 
            
            default:
                return redirect()->intended('/dashboard');
        }
    }
}