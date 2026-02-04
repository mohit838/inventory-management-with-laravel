@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center -mt-20">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
            <div class="p-8 bg-indigo-600 text-white text-center">
                <h2 class="text-3xl font-extrabold tracking-tight">Welcome Back</h2>
                <p class="mt-2 text-indigo-100 text-sm">Please enter your credentials to login</p>
            </div>
            
            <form method="POST" action="/login" class="p-8 space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                    <input id="email" type="email" name="email" required autofocus 
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all-200 outline-none"
                        placeholder="you@example.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                    <input id="password" type="password" name="password" required 
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all-200 outline-none"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" type="checkbox" name="remember" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-slate-600 font-medium">Remember me</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" 
                    class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all-200 transform hover:-translate-y-0.5 active:translate-y-0">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
