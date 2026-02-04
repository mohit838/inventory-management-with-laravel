@extends('layouts.app')

@section('header', 'Two-Factor Authentication')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50">
            <h3 class="text-xl font-bold text-slate-800">Secure Your Account</h3>
            <p class="text-slate-500 text-sm mt-1">Two-factor authentication adds an extra layer of security to your account.</p>
        </div>
        
        <div class="p-8 space-y-6 flex flex-col items-center">
            <div class="text-center space-y-4">
                <p class="text-sm text-slate-600">Scan this QR code with your authenticator app (like Google Authenticator or Authy) to get started.</p>
                
                <div class="p-4 bg-white border border-slate-200 rounded-2xl inline-block shadow-sm">
                    {!! $qrCodeUrl !!}
                </div>
                
                <div class="flex flex-col items-center">
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-2">Secret Key</p>
                    <code class="px-3 py-1 bg-slate-100 text-slate-800 rounded-lg font-mono text-sm break-all select-all">{{ $secret }}</code>
                </div>
            </div>

            <form action="{{ route('two-factor.enable') }}" method="POST" class="w-full max-w-sm space-y-4">
                @csrf
                <div>
                    <label for="one_time_password" class="block text-sm font-semibold text-slate-700 mb-2">Verification Code</label>
                    <input type="text" name="one_time_password" id="one_time_password" required autofocus
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all-200 outline-none text-center text-2xl tracking-[0.5em] font-bold"
                        maxlength="6" placeholder="000000">
                    @error('one_time_password') <p class="text-rose-500 text-xs mt-1 text-center font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" 
                        class="w-full py-4 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 transition-all-200 shadow-xl shadow-primary-100">
                        Enable 2FA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
