<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    /**
     * Show the 2FA setup page with QR code.
     */
    public function showSetupForm(Request $request)
    {
        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');

        // Always generate a fresh secret if not already enabled
        // or if they are re-setting it up
        $secret = $google2fa->generateSecretKey();
        
        // Temporarily store in session instead of DB
        session(['2fa_setup_secret' => $secret]);

        $inlineUrl = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('auth.two_factor_setup', [
            'qrCodeUrl' => $inlineUrl,
            'secret' => $secret
        ]);
    }

    /**
     * Enable 2FA after verifying a code from the user.
     */
    public function enable(Request $request)
    {
        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');
        $secret = session('2fa_setup_secret');

        if (!$secret) {
            return redirect()->route('two-factor.setup');
        }

        $request->validate([
            'one_time_password' => 'required|numeric',
        ]);

        $valid = $google2fa->verifyKey($secret, $request->one_time_password);

        if ($valid) {
            $user->google2fa_secret = $secret;
            $user->two_factor_enabled = true;
            $user->save();

            session()->forget('2fa_setup_secret');

            return redirect()->route('settings')->with('success', 'Two-factor authentication enabled successfully.');
        }

        return back()->withErrors(['one_time_password' => 'Invalid verification code. Please try again.']);
    }

    /**
     * Disable 2FA.
     */
    public function disable(Request $request)
    {
        $user = Auth::user();
        $user->two_factor_enabled = false;
        $user->google2fa_secret = null;
        $user->save();

        return back()->with('success', 'Two-factor authentication disabled.');
    }

    /**
     * Show the 2FA verification form during login.
     */
    public function showVerifyForm()
    {
        return view('auth.two_factor_verify');
    }

    /**
     * Verify the 2FA code during login.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|numeric',
        ]);

        // The middleware usually handles this, but since we intercepted login manually,
        // we should mark the session as verified using the package's logic.
        $google2fa = app('pragmarx.google2fa');
        $user = Auth::user();

        if ($google2fa->verifyKey($user->google2fa_secret, $request->one_time_password)) {
            // Log in the user into the 2FA session
            session([config('google2fa.session_var') => [
                'auth_passed' => true,
                'auth_attempt' => true,
            ]]);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['one_time_password' => 'Invalid verification code.']);
    }
}
