@extends('layouts.app')

@section('header', 'Page Not Found')

@section('content')
<div class="min-h-[60vh] flex flex-col items-center justify-center text-center p-8">
    <div class="relative">
        <h1 class="text-[12rem] font-black text-slate-200 leading-none select-none">404</h1>
        <div class="absolute inset-0 flex items-center justify-center">
            <h2 class="text-3xl font-bold text-indigo-600 uppercase tracking-widest bg-white/80 px-4 py-2 rounded-xl glass">Oops! Page Lost</h2>
        </div>
    </div>

    <p class="mt-8 text-slate-500 max-w-md text-lg font-medium leading-relaxed">
        The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
    </p>

    <div class="mt-12 flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6">
        <a href="{{ Auth::check() ? route('dashboard') : route('login') }}" 
            class="px-8 py-4 bg-indigo-600 text-white font-bold rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all-200 transform hover:-translate-y-1 active:translate-y-0">
            {{ Auth::check() ? 'Back to Dashboard' : 'Back to Login' }}
        </a>

        <button onclick="window.history.back()" 
            class="px-8 py-4 bg-white text-slate-600 font-bold border border-slate-200 rounded-2xl hover:bg-slate-50 transition-all-200">
            Go Back
        </button>
    </div>

    @auth
    <p class="mt-12 text-xs text-slate-400 font-medium tracking-tight">
        Logged in as: <span class="text-indigo-400">{{ Auth::user()->email }}</span> ({{ Auth::user()->tenant->name ?? 'Global' }})
    </p>
    @endauth
</div>
@endsection
