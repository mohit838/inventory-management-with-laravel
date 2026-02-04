@extends('layouts.app')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center">
    <div class="w-full max-w-lg">
        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/60 overflow-hidden border border-slate-100/80 p-12">
            <div class="text-center mb-10">
                <div class="w-16 h-16 bg-brand-50 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-3.632A9.959 9.959 0 0110 12c0-.422-.026-.838-.077-1.246M17.5 11.5L21 8M17.5 11.5L21 15M17.5 11.5l-6 6"></path></svg>
                </div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Recover Identity</h2>
                <p class="mt-2 text-slate-400 text-[11px] font-black uppercase tracking-[0.2em]">Initiating Security Key Restoration</p>
            </div>
            
            <form method="POST" action="{{ route('password.email') }}" class="space-y-7">
                @csrf
                <div>
                    <label for="email" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Verification Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all outline-none text-sm font-bold text-slate-700"
                        placeholder="name@enterprise.com">
                    @error('email') <p class="text-rose-500 text-[10px] font-bold mt-2 uppercase tracking-tight">{{ $message }}</p> @enderror
                </div>

                <button type="submit" 
                    class="w-full py-5 bg-brand-500 hover:bg-brand-600 text-white font-black uppercase tracking-[0.2em] text-[11px] rounded-2xl shadow-2xl shadow-brand-500/20 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center">
                    Dispatch Recovery Token
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </button>

                <div class="text-center pt-4">
                    <a href="{{ route('login') }}" class="text-[10px] font-black text-slate-400 hover:text-brand-600 uppercase tracking-[0.2em] transition-colors flex items-center justify-center">
                        <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Return to Gateway
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
