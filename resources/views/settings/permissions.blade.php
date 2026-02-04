@extends('layouts.app')

@section('header', 'Security Matrix')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Access Control Matrix</h3>
                <p class="text-slate-500 text-xs font-semibold mt-1 uppercase tracking-widest">Manage granular module permissions across roles</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 bg-brand-50 text-brand-600 text-[10px] font-black uppercase rounded-full border border-brand-100 italic">Superadmin Access Restricted</span>
            </div>
        </div>
        
        <form action="{{ route('settings.permissions.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">Permission / Module</th>
                            @foreach($roles as $role)
                            <th class="px-8 py-5 text-center border-b border-slate-100">
                                <span class="px-4 py-1.5 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-full shadow-lg shadow-slate-900/20">
                                    {{ $role->name }}
                                </span>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($groupedPermissions as $module => $permissions)
                        <tr class="bg-brand-50/30">
                            <td colspan="{{ count($roles) + 1 }}" class="px-8 py-2 text-[10px] font-black text-brand-600 uppercase tracking-[0.2em] italic border-b border-brand-100">
                                Module: {{ strtoupper($module) }}
                            </td>
                        </tr>
                        @foreach($permissions as $permission)
                        <tr class="group hover:bg-slate-50/30 transition-colors">
                            <td class="px-8 py-5">
                                <p class="text-sm font-bold text-slate-700 group-hover:text-brand-600 transition-colors">{{ ucwords(str_replace('_' . str_replace(' ', '_', $module), '', $permission->name)) }} Action</p>
                                <p class="text-[10px] text-slate-400 font-medium mt-0.5">{{ $permission->name }}</p>
                            </td>
                            @foreach($roles as $role)
                            <td class="px-8 py-5 text-center">
                                <label class="relative inline-flex items-center cursor-pointer group/check">
                                    <input type="checkbox" name="permissions[{{ $role->id }}][]" value="{{ $permission->name }}" 
                                        {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-500/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-500 shadow-inner"></div>
                                </label>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-8 bg-slate-50/80 flex justify-end">
                <button type="submit" 
                    class="px-10 py-3.5 bg-brand-500 text-white font-black uppercase tracking-widest text-[11px] rounded-2xl hover:bg-brand-600 transition-all duration-300 shadow-2xl shadow-brand-500/20 active:scale-95 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    Apply Security Overrides
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
