@extends('layouts.app')

@section('header', 'Infrastructure Oversight')

@section('content')
<div class="space-y-10">
    <!-- Top Level Global Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex items-center group hover:border-brand-100 transition-all">
            <div class="w-16 h-16 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center mr-6 shadow-inner shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Total System Users</p>
                <p class="text-4xl font-black text-slate-800 tracking-tight leading-none">{{ $stats['total_users'] }}</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex items-center group hover:border-emerald-100 transition-all">
            <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mr-6 shadow-inner shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Active & Healthy</p>
                <p class="text-4xl font-black text-slate-800 tracking-tight leading-none">{{ $stats['active_users'] }}</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex items-center group hover:border-rose-100 transition-all">
            <div class="w-16 h-16 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mr-6 shadow-inner shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Inactive Accounts</p>
                <p class="text-4xl font-black text-slate-800 tracking-tight leading-none">{{ $stats['inactive_users'] }}</p>
            </div>
        </div>
    </div>

    <!-- Role Distribution & Regional Distribution (MOCKED) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-slate-100">
            <h3 class="text-lg font-black text-slate-800 tracking-tight mb-8">Access Hierarchy</h3>
            <div class="space-y-6">
                @foreach($roles as $name => $count)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-2 h-2 rounded-full mr-4 {{ $name === 'superadmin' ? 'bg-slate-900' : ($name === 'admin' ? 'bg-brand-500' : 'bg-slate-200') }}"></div>
                        <p class="text-sm font-bold text-slate-600 capitalize">{{ $name }}</p>
                    </div>
                    <span class="px-4 py-1 bg-slate-50 text-slate-800 text-xs font-black rounded-lg">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-slate-900 p-10 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
            <!-- Decorative abstract SVG -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-brand-500/20 rounded-full blur-[100px] group-hover:bg-brand-500/30 transition-all duration-1000"></div>
            
            <h3 class="text-lg font-black text-white tracking-tight relative z-10">Infrastructure Health</h3>
            <p class="text-slate-400 text-xs mt-1 relative z-10 uppercase tracking-widest">Global Server Latency & Uptime</p>
            
            <div class="mt-12 flex items-end justify-between space-x-2 relative z-10">
                @for($i=0; $i<12; $i++)
                <div class="w-full bg-brand-500/20 rounded-t-lg transition-all duration-500 group-hover:bg-brand-500/40" style="height: {{ rand(20, 100) }}px"></div>
                @endfor
            </div>
            <p class="text-[10px] text-brand-400 font-bold mt-6 relative z-10 uppercase tracking-[0.2em]">Operational: 99.99%</p>
        </div>
    </div>

    <!-- Tenant Performance Table -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200/60 overflow-hidden">
        <div class="p-10 border-b border-slate-50 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-black text-slate-800 tracking-tight">Active Tenant Performance</h3>
                <p class="text-slate-400 text-xs font-bold mt-1 uppercase tracking-widest">Cross-tenant member distribution & revenue insights</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Organization</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Total Members</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Owners</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Employees</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($tenants as $tenant)
                    <tr class="hover:bg-slate-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-700 tracking-tight">{{ $tenant['name'] }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-black text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">{{ $tenant['total_members'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-bold text-brand-600 bg-brand-50 px-2.5 py-1 rounded-lg border border-brand-100">{{ $tenant['owners'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">{{ $tenant['employees'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Component Integration -->
        <div class="p-6 bg-white border-t border-slate-50">
            {{ $tenants->links('components.tables.pagination') }}
        </div>
    </div>
</div>
@endsection
