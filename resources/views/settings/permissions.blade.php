@extends('layouts.app')

@section('header', 'Security Infrastructure')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200/60 overflow-hidden">
        <div class="p-10 border-b border-slate-50 flex items-center justify-between bg-white sticky top-0 z-20">
            <div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Security Matrix</h3>
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em] mt-1.5">Infrastructure Access & Module Permissions Control</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Sorted by Role Priority</span>
                <button type="submit" form="permissions-form" class="px-8 py-3 bg-brand-500 text-white font-black uppercase tracking-[0.2em] text-[10px] rounded-2xl hover:bg-brand-600 transition-all shadow-xl shadow-brand-500/20 active:scale-95">
                    Deploy Updates
                </button>
            </div>
        </div>

        <form id="permissions-form" action="{{ route('settings.permissions.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left table-fixed min-w-[1200px]">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="w-1/3 px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest sticky left-0 bg-slate-50/50 z-10 border-r border-slate-100/50">Access Point / Module Action</th>
                            @foreach($roles as $role)
                            <th class="px-10 py-6 text-center border-r border-slate-50 last:border-r-0">
                                <p class="text-[10px] font-black text-slate-800 uppercase tracking-widest">{{ $role->name }}</p>
                                <p class="text-[9px] text-slate-400 font-bold tracking-tighter mt-1">Role Group Index: 0{{ $loop->iteration }}</p>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($groupedPermissions as $module => $permissions)
                        <tr class="bg-brand-50/20">
                            <td colspan="{{ count($roles) + 1 }}" class="px-10 py-3 text-[10px] font-black text-brand-600 uppercase tracking-[0.2em] italic border-b border-brand-100/50 sticky left-0 z-10">
                                Module Scope: {{ strtoupper($module) }}
                            </td>
                        </tr>
                        @foreach($permissions as $permission)
                        <tr class="group hover:bg-slate-50/30 transition-colors">
                            <td class="px-10 py-6 sticky left-0 bg-white group-hover:bg-slate-50/30 transition-colors z-10 border-r border-slate-100/50 shadow-[5px_0_15px_-5px_rgba(0,0,0,0.05)]">
                                <div class="flex items-center">
                                    <div class="w-1.5 h-1.5 rounded-full bg-brand-500 mr-4 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    <div>
                                        <p class="text-sm font-black text-slate-700 group-hover:text-brand-600 transition-colors">{{ ucwords(str_replace('_' . str_replace(' ', '_', $module), '', $permission->name)) }} Action</p>
                                        <p class="text-[10px] text-slate-400 font-medium mt-0.5 tracking-tight uppercase">{{ $permission->name }}</p>
                                    </div>
                                </div>
                            </td>
                            @foreach($roles as $role)
                            <td class="px-10 py-6 text-center border-r border-slate-50 last:border-r-0">
                                <label class="relative inline-flex items-center cursor-pointer group/check">
                                    <input type="checkbox" name="permissions[{{ $role->id }}][]" value="{{ $permission->name }}" 
                                        {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-500/10 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand-500 shadow-inner group-hover/check:scale-110 transition-transform"></div>
                                </label>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
        
        <div class="p-10 bg-slate-50/50 border-t border-slate-100/80 flex items-center justify-between">
            <p class="text-[11px] font-bold text-slate-400 max-w-lg italic">Note: Changes made here update Infrastructure Access Control Lists (ACL) in real-time. Please deploy with caution.</p>
            <button type="submit" form="permissions-form" class="px-12 py-4 bg-slate-900 text-white font-black uppercase tracking-[0.2em] text-[10px] rounded-2xl hover:bg-black transition-all shadow-2xl shadow-slate-900/20 active:scale-95">
                Commit & Finalize Updates
            </button>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
@endsection
