<?php

namespace App\Http\Controllers;

use App\Services\InvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    public function __construct(protected InvitationService $invitationService)
    {
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            if ($user->two_factor_enabled) {
                // Logout immediately to prevent unauthorized access until 2FA is verified in session
                // Actually, the package manages session verified status. 
                // We'll redirect to the verify page.
                return redirect()->route('two-factor.verify');
            }

            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegistrationForm($token)
    {
        $invitation = $this->invitationService->validateToken($token);

        if (!$invitation) {
            return redirect('/login')->with('error', 'Invalid or expired invitation link.');
        }

        return view('auth.register', compact('invitation'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'tenant_name' => 'sometimes|required|string|max:255',
        ]);

        $invitation = $this->invitationService->validateToken($request->token);

        if (!$invitation) {
            return redirect('/login')->with('error', 'Invalid or expired invitation link.');
        }

        $user = $this->invitationService->acceptInvitation($invitation, $request->all());

        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Account created successfully!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['success' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
