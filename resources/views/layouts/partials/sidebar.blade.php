<aside 
    :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 w-72 bg-sidebar text-slate-400 z-50 transition-transform duration-300 ease-in-out lg:static lg:block flex flex-col border-r border-slate-800/40 h-full">
    
    <!-- Logo Section -->
    <div class="h-20 flex items-center px-8 mb-6 flex-shrink-0">
        <img src="{{ asset('favicon-logo.png') }}" class="w-10 h-10 mr-3.5 shadow-lg shadow-brand-500/20 rotate-3 transition-transform duration-300" alt="EIMS Logo">
        <div>
            <h1 class="text-white font-black text-2xl tracking-tighter uppercase">INV</h1>
            <div class="flex items-center">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-500 mr-1.5"></span>
                <span class="text-[9px] text-slate-500 font-black uppercase tracking-[0.15em]">{{ Auth::user()->tenant->name ?? 'Enterprise' }}</span>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto sidebar-scroll">
        <div>
            <p class="px-4 text-[10px] font-black text-slate-600 uppercase tracking-[0.25em] mb-3 mt-4">Platform</p>
            
            @can('view_dashboard')
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'sidebar-item-active text-white' : 'hover:bg-slate-800/40 hover:text-slate-200' }}">
                <svg class="w-5 h-5 mr-3.5 {{ request()->routeIs('dashboard') ? 'text-brand-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard
            </a>
            @endcan

            @can('view_infrastructure')
            <a href="{{ route('superadmin.dashboard') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 group {{ request()->routeIs('superadmin.dashboard') ? 'sidebar-item-active text-white' : 'hover:bg-slate-800/40 hover:text-slate-200' }}">
                <svg class="w-5 h-5 mr-3.5 {{ request()->routeIs('superadmin.dashboard') ? 'text-brand-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Infrastructure
            </a>
            @endcan

            @can('manage_requests')
            <a href="{{ route('superadmin.requests') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 group {{ request()->routeIs('superadmin.requests') ? 'sidebar-item-active text-white' : 'hover:bg-slate-800/40 hover:text-slate-200' }}">
                <svg class="w-5 h-5 mr-3.5 {{ request()->routeIs('superadmin.requests') ? 'text-brand-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                Onboarding Portal
            </a>
            @endcan

            @can('view_diagnostics')
            <a href="{{ route('system.health') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 group {{ request()->routeIs('system.health') ? 'sidebar-item-active text-white' : 'hover:bg-slate-800/40 hover:text-slate-200' }}">
                <svg class="w-5 h-5 mr-3.5 {{ request()->routeIs('system.health') ? 'text-brand-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                System Health
            </a>
            @endcan
        </div>

        @if(Auth::user()->canAny(['create_invitations', 'view_users']))
        <div>
            <p class="px-4 text-[10px] font-black text-slate-600 uppercase tracking-[0.25em] mb-3 mt-8">Operations</p>
            
            @can('create_invitations')
            <a href="{{ route('invitations.create') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 group {{ request()->routeIs('invitations.*') ? 'sidebar-item-active text-white' : 'hover:bg-slate-800/40 hover:text-slate-200' }}">
                <svg class="w-5 h-5 mr-3.5 {{ request()->routeIs('invitations.*') ? 'text-brand-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Team Growth
            </a>
            @endcan

            @can('view_users')
            <a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 group {{ request()->routeIs('users.*') ? 'sidebar-item-active text-white' : 'hover:bg-slate-800/40 hover:text-slate-200' }}">
                <svg class="w-5 h-5 mr-3.5 {{ request()->routeIs('users.*') ? 'text-brand-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Personnel
            </a>
            @endcan
        </div>
        @endif

        @if(Auth::user()->canAny(['view_settings', 'manage_permissions']))
        <div>
            <p class="px-4 text-[10px] font-black text-slate-600 uppercase tracking-[0.25em] mb-3 mt-8">System</p>
            @can('view_settings')
            <a href="{{ route('settings') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 group {{ request()->routeIs('settings') && !request()->routeIs('settings.permissions') ? 'sidebar-item-active text-white' : 'hover:bg-slate-800/40 hover:text-slate-200' }}">
                <svg class="w-5 h-5 mr-3.5 {{ request()->routeIs('settings') && !request()->routeIs('settings.permissions') ? 'text-brand-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Core Settings
            </a>
            @endcan

            @can('manage_permissions')
            <a href="{{ route('settings.permissions') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 group {{ request()->routeIs('settings.permissions') ? 'sidebar-item-active text-white' : 'hover:bg-slate-800/40 hover:text-slate-200' }}">
                <svg class="w-5 h-5 mr-3.5 {{ request()->routeIs('settings.permissions') ? 'text-brand-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Security Matrix
            </a>
            @endcan
        </div>
        @endif
    </nav>

    <!-- Profile at Bottom -->
    <div class="mt-auto p-4 border-t border-slate-800/40 pb-8 flex-shrink-0">
        <div class="flex items-center p-3.5 bg-slate-950/40 rounded-2xl border border-slate-800/50 group">
            <div class="relative flex-shrink-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center text-white font-black shadow-xl shadow-brand-950/50">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-emerald-500 border-[3px] border-sidebar rounded-full shadow-lg"></div>
            </div>
            <div class="ml-3.5 overflow-hidden flex-1">
                <p class="text-slate-100 text-sm font-bold truncate leading-tight">{{ Auth::user()->name }}</p>
                <p class="text-[9px] text-slate-500 font-black uppercase tracking-widest mt-0.5">{{ Auth::user()->roles->first()->name ?? 'Member' }}</p>
            </div>
        </div>
    </div>
</aside>
