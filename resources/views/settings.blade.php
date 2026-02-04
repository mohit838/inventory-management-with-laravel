@extends('layouts.app')

@section('header', 'Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50">
            <h3 class="text-xl font-bold text-slate-800">Account Settings</h3>
            <p class="text-slate-500 text-sm mt-1">Manage your official organization and preferences.</p>
        </div>
        
        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1">
                    <h4 class="font-semibold text-slate-800">Organization Info</h4>
                    <p class="text-xs text-slate-400 mt-1">This will be visible to all members in your tenant.</p>
                </div>
                <div class="lg:col-span-2 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Tenant Name</label>
                        <input type="text" value="{{ Auth::user()->tenant->name ?? 'Global' }}" disabled 
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-500 font-medium cursor-not-allowed">
                    </div>
                </div>
            </div>

            <hr class="border-slate-50">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1">
                    <h4 class="font-semibold text-slate-800">Security</h4>
                    <p class="text-xs text-slate-400 mt-1">Update your password to keep your account safe.</p>
                </div>
                <div class="lg:col-span-2 space-y-4">
                    <a href="{{ route('password.change') }}" class="inline-block px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-200 transition-all-200">
                        Change Password
                    </a>
                </div>
            </div>
        </div>

        <hr class="border-slate-50">

        <div class="p-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1">
                    <h4 class="font-semibold text-slate-800">Two-Factor Authentication</h4>
                    <p class="text-xs text-slate-400 mt-1">Add additional security to your account using 2FA.</p>
                </div>
                <div class="lg:col-span-2 flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <div>
                        <p class="text-sm font-bold text-slate-700">Google Authenticator </p>
                        <p class="text-xs text-slate-500">{{ Auth::user()->two_factor_enabled ? 'Active and protecting your account.' : 'Not enabled yet.' }}</p>
                    </div>
                    @if(Auth::user()->two_factor_enabled)
                        <form x-data="{ 
                            submitForm(e) {
                                e.preventDefault();
                                confirmAction({
                                    title: 'Disable Protection?',
                                    text: 'Removing 2FA reduces your account security. Are you sure?',
                                    confirmButtonText: 'Yes, Disable'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        this.$el.submit();
                                    }
                                });
                            }
                        }" @submit="submitForm" action="{{ route('two-factor.disable') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-rose-50 text-rose-600 text-xs font-bold rounded-lg border border-rose-100 hover:bg-rose-100 transition-all">
                                Disable
                            </button>
                        </form>
                    @else
                        <a href="{{ route('two-factor.setup') }}" class="px-4 py-2 bg-brand-500 text-white text-xs font-bold rounded-lg hover:bg-brand-600 transition-all shadow-md shadow-brand-500/20">
                            Enable 2FA
                        </a>
                    @endif
                </div>
            </div>
        </div>
        
        @can('manage_settings')
        <div class="p-8 bg-slate-50 flex justify-end">
            <button class="px-8 py-3 bg-brand-500 text-white font-black uppercase tracking-widest text-[11px] rounded-2xl hover:bg-brand-600 transition-all duration-300 shadow-xl shadow-brand-500/20 active:scale-95">
                Save All Changes
            </button>
        </div>
        @endcan
    </div>
</div>
@endsection
