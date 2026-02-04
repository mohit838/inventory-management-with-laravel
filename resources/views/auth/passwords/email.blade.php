@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center -mt-20">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
            <div class="p-8 bg-indigo-600 text-white text-center">
                <h2 class="text-3xl font-extrabold tracking-tight">Forgot Password?</h2>
                <p class="mt-2 text-indigo-100 text-sm">No problem. Just let us know your email address and we will email you a password reset link.</p>
            </div>
            
            <form method="POST" action="{{ route('password.email') }}" class="p-8 space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                    <input id="email" type="email" name="email" :value="old('email')" required autofocus 
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all-200 outline-none"
                        placeholder="you@example.com">
                    @error('email') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit" 
                    class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all-200 transform hover:-translate-y-0.5 active:translate-y-0">
                    Email Password Reset Link
                </button>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
