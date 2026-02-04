@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center -mt-10">
    <div class="w-full max-w-lg">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-100">
            <div class="p-8 bg-indigo-600 text-white text-center">
                <h2 class="text-3xl font-extrabold tracking-tight">Finish Your Signup</h2>
                <p class="mt-2 text-indigo-100 text-sm">Welcome! You've been invited as a <strong>{{ ucfirst($invitation->role) }}</strong></p>
            </div>
            
            <form method="POST" action="/register" class="p-10 space-y-8">
                @csrf
                <input type="hidden" name="token" value="{{ $invitation->token }}">
                
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1 uppercase tracking-wider">Email Address (Read Only)</label>
                    <div class="px-4 py-4 bg-slate-100 border border-slate-200 rounded-xl text-slate-600 font-medium">
                        {{ $invitation->email }}
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Full Name</label>
                    <input id="name" type="text" name="name" required autofocus 
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all-200 outline-none placeholder-slate-400"
                        placeholder="John Doe">
                </div>

                @if($invitation->role === 'owner')
                <div>
                    <label for="tenant_name" class="block text-sm font-semibold text-slate-700 mb-2">Organization Name</label>
                    <input id="tenant_name" type="text" name="tenant_name" required 
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all-200 outline-none placeholder-slate-400"
                        placeholder="Acme Corp">
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                        <input id="password" type="password" name="password" required 
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all-200 outline-none"
                            placeholder="••••••••">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required 
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all-200 outline-none"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" 
                        class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-lg rounded-2xl shadow-xl shadow-indigo-200 transition-all-200 transform hover:-translate-y-1 active:translate-y-0">
                        Complete Registration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
