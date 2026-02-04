@extends('layouts.app')

@section('content')
<div class="min-h-[90vh] flex items-center justify-center">
    <div class="w-full max-w-2xl">
        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/60 overflow-hidden border border-slate-100/80 p-12">
            <div class="text-center mb-10">
                <div class="w-16 h-16 bg-brand-50 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Onboarding Gateway</h2>
                <p class="mt-2 text-slate-400 text-[11px] font-black uppercase tracking-[0.2em]">Deploying Credentials for <strong>{{ strtoupper($invitation->role) }}</strong> Access</p>
            </div>
            
            <form method="POST" action="/register" class="space-y-8">
                @csrf
                <input type="hidden" name="token" value="{{ $invitation->token }}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Reserved Email</label>
                        <div class="px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-400 font-bold text-sm cursor-not-allowed">
                            {{ $invitation->email }}
                        </div>
                    </div>

                    <div>
                        <label for="name" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Full Legal Name</label>
                        <input id="name" type="text" name="name" required autofocus 
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all outline-none text-sm font-bold text-slate-700"
                            placeholder="e.g. Alexander Pierce">
                    </div>
                </div>

                @if($invitation->role === 'owner')
                <div>
                    <label for="tenant_name" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Organization Identity</label>
                    <input id="tenant_name" type="text" name="tenant_name" required 
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all outline-none text-sm font-bold text-slate-700"
                        placeholder="e.g. Global Logistics Inc.">
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label for="password" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Primary Security Key</label>
                        <input id="password" type="password" name="password" required 
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all outline-none text-sm font-bold text-slate-700"
                            placeholder="••••••••">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Confirm Key</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required 
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all outline-none text-sm font-bold text-slate-700"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" 
                        class="w-full py-5 bg-brand-500 hover:bg-brand-600 text-white font-black uppercase tracking-[0.2em] text-[11px] rounded-2xl shadow-2xl shadow-brand-500/20 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center">
                        Establish Infrastructure Profile
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
