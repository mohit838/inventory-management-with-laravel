@extends('layouts.app')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center">
    <div class="w-full max-w-lg">
        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/60 overflow-hidden border border-slate-100/80 p-12">
            <div class="text-center mb-10">
                <div class="w-16 h-16 bg-brand-50 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Identity Gateway</h2>
                <p class="mt-2 text-slate-400 text-[11px] font-black uppercase tracking-[0.2em]">Secure Entry for Infrastructure Growth</p>
            </div>
            
            <form method="POST" action="/login" class="space-y-7">
                @csrf
                <div>
                    <label for="email" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Professional Email</label>
                    <input id="email" type="email" name="email" required autofocus 
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all outline-none text-sm font-bold text-slate-700"
                        placeholder="name@enterprise.com">
                </div>

                <div>
                    <label for="password" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Security Key</label>
                    <input id="password" type="password" name="password" required 
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all outline-none text-sm font-bold text-slate-700"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <label class="relative inline-flex items-center cursor-pointer group">
                        <input id="remember" type="checkbox" name="remember" class="sr-only peer">
                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-500/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand-500"></div>
                        <span class="ml-3 text-xs font-bold text-slate-500 group-hover:text-slate-700 transition-colors">Keep me signed in</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-black text-brand-600 hover:text-brand-700 uppercase tracking-widest">Lost Key?</a>
                    @endif
                </div>

                <button type="submit" 
                    class="w-full py-5 bg-brand-500 hover:bg-brand-600 text-white font-black uppercase tracking-[0.2em] text-[11px] rounded-2xl shadow-2xl shadow-brand-500/20 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center">
                    Decrypt & Authenticate
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
