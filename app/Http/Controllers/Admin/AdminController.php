<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminController extends Controller
{
    // Registration Form
    public function create()
    {
        return view('admin.auth.register');
    }

    // Store Registration
// Show the registration form
    public function showRegisterForm()
    {
        return view('admin.auth.register');
    }

    // Handle admin registration
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|confirmed|min:8',
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.login')->with('success', 'Admin registered successfully!');
    }

    // Show the login form
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    // Handle admin login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt($request->only('email', 'password'))) {
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    // Show the forgot password form
    public function showForgotPasswordForm()
    {
        return view('admin.auth.forgot-password');
    }

    // Handle forgot password
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('admins')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    // Password Reset Form
    public function resetPasswordForm($token)
    {
        return view('admin.auth.reset-password', compact('token'));
    }

    // Handle Password Reset
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'token' => 'required',
        ]);

        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($admin, $password) {
                $admin->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }


    // Email Verification Notice
    public function verificationNotice()
    {
        return view('admin.auth.verify-email');
    }

    // Email Verification Logic
    public function verifyEmail($id, $hash)
    {
        // Implement email verification logic
        return redirect()->route('admin.dashboard')->with('success', 'Email verified successfully!');
    }
    // Confirm Password Form
    public function confirmPasswordForm()
    {
        return view('admin.auth.confirm-password');
    }

    // Handle Confirm Password
    public function confirmPassword(Request $request)
    {
        $request->validate(['password' => 'required']);

        if (Hash::check($request->password, Auth::guard('admin')->user()->password)) {
            return redirect()->intended();
        }

        return back()->withErrors(['password' => 'Password confirmation failed']);
    }

    // Update Password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $admin = Auth::guard('admin')->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $admin->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password updated successfully!');
    }

    // Logout
    public function destroy()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
