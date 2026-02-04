@extends('layouts.app')

@section('header', 'User Management')

@section('content')
<div class="space-y-6">
    <!-- Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('users.index') }}" method="GET" class="relative group max-w-sm w-full">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 group-focus-within:text-brand-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" name="search" value="{{ request('search') }}" 
                class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none transition-all text-sm"
                placeholder="Search by name or email...">
        </form>

        @can('create_invitations')
        <a href="{{ route('invitations.create') }}" class="inline-flex items-center px-5 py-2.5 bg-brand-600 text-white text-sm font-bold rounded-xl hover:bg-brand-700 transition-all shadow-lg shadow-brand-100">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            Invite Member
        </a>
        @endcan
    </div>

    <!-- User Table -->
    <x-tables.table>
        <x-tables.thead>
            <x-tables.tr>
                <x-tables.th>User</x-tables.th>
                <x-tables.th>Role</x-tables.th>
                <x-tables.th>Organization</x-tables.th>
                <x-tables.th>Status</x-tables.th>
                <x-tables.th class="text-right">Actions</x-tables.th>
            </x-tables.tr>
        </x-tables.thead>
        <x-tables.tbody>
            @forelse($users as $user)
            <x-tables.tr>
                <x-tables.td>
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500 mr-3 border border-slate-200">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-slate-900 font-bold leading-none">{{ $user->name }}</p>
                            <p class="text-xs text-slate-400 mt-1">{{ $user->email }}</p>
                        </div>
                    </div>
                </x-tables.td>
                <x-tables.td>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-600 border border-indigo-100">
                        {{ $user->roles->first()->name ?? 'No Role' }}
                    </span>
                </x-tables.td>
                <x-tables.td>
                    <span class="text-slate-500 text-xs">{{ $user->tenant->name ?? 'Global' }}</span>
                </x-tables.td>
                <x-tables.td>
                    @if($user->trashed())
                        <span class="inline-flex items-center text-rose-500 text-xs font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-2"></span>
                            Inactive
                        </span>
                    @else
                        <span class="inline-flex items-center text-emerald-500 text-xs font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2"></span>
                            Active
                        </span>
                    @endif
                </x-tables.td>
                <x-tables.td class="text-right">
                    @if($user->id !== Auth::id())
                        @can('delete_users')
                            @if($user->trashed())
                                <form action="{{ route('users.restore', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-brand-50 text-brand-600 hover:bg-brand-600 hover:text-white rounded-full text-[10px] font-black uppercase tracking-widest transition-all duration-300">Activate</button>
                                </form>
                            @else
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Deactivate this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-full text-[10px] font-black uppercase tracking-widest transition-all duration-300">Deactivate</button>
                                </form>
                            @endif
                        @endcan
                    @endif
                </x-tables.td>
            </x-tables.tr>
            @empty
            <x-tables.tr>
                <x-tables.td colspan="5" class="py-10 text-center text-slate-400 italic">
                    No users found matching your criteria.
                </x-tables.td>
            </x-tables.tr>
            @endforelse
        </x-tables.tbody>
    </x-tables.table>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $users->links('components.tables.pagination') }}
    </div>
</div>
@endsection
