@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-100 animate-in fade-in zoom-in duration-300">
            <div class="p-8 bg-primary-600 text-white text-center">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <h2 class="text-3xl font-extrabold tracking-tight">Verify Identity</h2>
                <p class="mt-2 text-primary-100 text-sm">Enter the code from your app to continue</p>
            </div>
            
            <form method="POST" action="{{ route('two-factor.verify') }}" class="p-10 space-y-8">
                @csrf
                <div>
                    <label for="one_time_password" class="block text-sm font-bold text-slate-500 uppercase tracking-widest text-center mb-6">Authenticator Code</label>
                    <input id="one_time_password" type="text" name="one_time_password" required autofocus maxlength="6"
                        class="w-full px-5 py-5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all-200 outline-none text-center text-4xl font-black tracking-[0.3em] text-slate-800"
                        placeholder="000000">
                    @error('one_time_password') <p class="text-rose-500 text-xs mt-3 text-center font-bold">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" 
                        class="w-full py-5 bg-primary-600 hover:bg-primary-700 text-white font-black text-xl rounded-2xl shadow-2xl shadow-primary-200 transition-all-200 transform hover:-translate-y-1 active:translate-y-0">
                        Confirm & Login
                    </button>
                </div>
                
                <div class="text-center">
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-sm font-bold text-slate-400 hover:text-rose-500 transition-colors">
                        Cancel & Sign Out
                    </a>
                </div>
            </form>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </div>
    </div>
</div>
@endsection
