<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $users = [];
        if ($this->canManageUsers($user)) {
            $users = User::orderBy('name')->get();
        }
        return view('profile', compact('user', 'users'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function storeUser(Request $request)
    {
        $this->authorizeUserManagement();

        $validated = $request->validate([
            'user_id' => 'required|string|max:255|unique:users,user_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|max:255',
        ]);

        User::create([
            'user_id' => $validated['user_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return back()->with('success', 'User created successfully.');
    }

    public function updateRole(Request $request, $userId)
    {
        $this->authorizeUserManagement();

        $validated = $request->validate([
            'role' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($userId);
        $user->role = $validated['role'];
        $user->save();

        return back()->with('success', 'User role updated successfully.');
    }

    public function destroyUser($userId)
    {
        $this->authorizeUserManagement();

        if (Auth::id() === $userId) {
            return back()->withErrors(['error' => 'You cannot delete yourself.']);
        }

        $user = User::findOrFail($userId);
        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }

    private function canManageUsers($user): bool
    {
        return $user && in_array(strtolower($user->role ?? ''), ['project manager', 'admin']);
    }

    private function authorizeUserManagement(): void
    {
        if (!$this->canManageUsers(Auth::user())) {
            abort(403, 'Only Project Manager and Admin can perform this action.');
        }
    }
}


