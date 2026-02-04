@extends('layouts.app')

@section('header', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Stat Card -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-brand-50 text-brand-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">+12%</span>
        </div>
        <h3 class="text-slate-500 text-sm font-medium">Total Users</h3>
        <p class="text-2xl font-bold text-slate-800 mt-1">24</p>
    </div>

    <!-- Stat Card -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">+5%</span>
        </div>
        <h3 class="text-slate-500 text-sm font-medium">Monthly Orders</h3>
        <p class="text-2xl font-bold text-slate-800 mt-1">1,482</p>
    </div>

    <!-- Stat Card -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-full">-2%</span>
        </div>
        <h3 class="text-slate-500 text-sm font-medium">Pending Tasks</h3>
        <p class="text-2xl font-bold text-slate-800 mt-1">18</p>
    </div>

    <!-- Stat Card -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">+22%</span>
        </div>
        <h3 class="text-slate-500 text-sm font-medium">Total Revenue</h3>
        <p class="text-2xl font-bold text-slate-800 mt-1">$42,390</p>
    </div>
</div>

<div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
        <h3 class="text-lg font-bold text-slate-800 mb-6">Recent Activities</h3>
        <div class="space-y-6">
            @for ($i = 0; $i < 4; $i++)
            <div class="flex items-start space-x-4">
                <div class="w-2 h-2 mt-2 rounded-full bg-brand-500"></div>
                <div>
                    <p class="text-sm font-semibold text-slate-700">New order processed #TRX-9482</p>
                    <p class="text-xs text-slate-400 mt-1">2 hours ago</p>
                </div>
            </div>
            @endfor
        </div>
    </div>
    
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center justify-center text-center">
        <div class="w-20 h-20 bg-brand-50 text-brand-600 rounded-full flex items-center justify-center mb-4">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z"></path></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-800">Ready to expand?</h3>
        <p class="text-slate-500 mt-2 max-w-sm">Invite your team members to collaborate on this project.</p>
        @can('create_invitations')
        <a href="{{ route('invitations.create') }}" class="mt-6 px-10 py-3.5 bg-brand-500 text-white font-black uppercase tracking-widest text-[11px] rounded-2xl hover:bg-brand-600 transition-all duration-300 shadow-2xl shadow-brand-500/20 active:scale-95 inline-block">
            New Team Invitation
        </a>
        @endcan
    </div>
</div>
@endsection
