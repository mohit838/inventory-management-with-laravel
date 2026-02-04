@extends('layouts.app')

@section('header', 'Change Password')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50">
            <h3 class="text-xl font-bold text-slate-800">Security Settings</h3>
            <p class="text-slate-500 text-sm mt-1">Ensure your account is using a long, random password to stay secure.</p>
        </div>
        
        <form action="{{ route('password.update.profile') }}" method="POST" class="p-8 space-y-6">
            @csrf
            <div>
                <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-2">Current Password</label>
                <input type="password" name="current_password" id="current_password" required 
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all-200 outline-none"
                    placeholder="••••••••">
                @error('current_password') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">New Password</label>
                <input type="password" name="password" id="password" required 
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all-200 outline-none"
                    placeholder="••••••••">
                @error('password') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required 
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all-200 outline-none"
                    placeholder="••••••••">
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" 
                    class="px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all-200 shadow-lg shadow-indigo-100">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
