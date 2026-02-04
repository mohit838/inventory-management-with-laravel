@extends('layouts.app')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center">
    <div class="w-full max-w-lg">
        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/60 overflow-hidden border border-slate-100/80 p-12">
            <div class="text-center mb-10">
                <div class="w-16 h-16 bg-brand-50 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                </div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Finalize Recovery</h2>
                <p class="mt-2 text-slate-400 text-[11px] font-black uppercase tracking-[0.2em]">Deploying New Security Credentials</p>
            </div>
            
            <form method="POST" action="{{ route('password.update') }}" class="space-y-7">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Linked Identity</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all outline-none text-sm font-bold text-slate-700"
                        placeholder="name@enterprise.com">
                    @error('email') <p class="text-rose-500 text-[10px] font-bold mt-2 uppercase tracking-tight">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">New Security Key</label>
                    <input id="password" type="password" name="password" required 
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all outline-none text-sm font-bold text-slate-700"
                        placeholder="••••••••">
                    @error('password') <p class="text-rose-500 text-[10px] font-bold mt-2 uppercase tracking-tight">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Verify Key</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required 
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all outline-none text-sm font-bold text-slate-700"
                        placeholder="••••••••">
                </div>

                <button type="submit" 
                    class="w-full py-5 bg-brand-500 hover:bg-brand-600 text-white font-black uppercase tracking-[0.2em] text-[11px] rounded-2xl shadow-2xl shadow-brand-500/20 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center">
                    Update Security Identity
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
