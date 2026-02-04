@extends('layouts.app')

@section('header', 'Invite Team Member')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50">
            <h3 class="text-xl font-bold text-slate-800">Send an Invitation</h3>
            <p class="text-slate-500 text-sm mt-1">Send a 24-hour valid invitation link to a new member.</p>
        </div>
        
        <form action="{{ route('invitations.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                <input type="email" name="email" id="email" required 
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all-200 outline-none"
                    placeholder="teammate@example.com">
                @error('email') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="role" class="block text-sm font-semibold text-slate-700 mb-2">Assign Role</label>
                <select name="role" id="role" required 
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all-200 outline-none appearance-none">
                    @if(Auth::user()->hasRole(['superadmin', 'admin']))
                        <option value="owner">Owner (Brand New Tenant)</option>
                        <option value="admin">Admin (Global)</option>
                    @endif
                    <option value="employee">Employee (In your Organization)</option>
                </select>
                <p class="text-[10px] text-slate-400 mt-2 italic">* Roles are restricted based on your own permissions.</p>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" 
                    class="px-8 py-3 bg-brand-500 text-white font-black uppercase tracking-widest text-[11px] rounded-2xl hover:bg-brand-600 transition-all duration-300 shadow-xl shadow-brand-500/20 active:scale-95">
                    Send Invitation Link
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
