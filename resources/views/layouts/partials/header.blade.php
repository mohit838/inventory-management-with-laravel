<header class="h-20 glass-header border-b border-slate-200/60 flex items-center justify-between px-8 sticky top-0 z-30 flex-shrink-0">
    <div class="flex items-center space-x-6">
        <button @click="mobileMenuOpen = true" class="p-2.5 -ml-2 text-slate-500 hover:bg-slate-100 rounded-xl lg:hidden transition-all duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
        </button>
        <div>
            <h2 class="text-xl font-black text-slate-800 tracking-tight leading-none">@yield('header')</h2>
        </div>
    </div>

    <div class="flex items-center space-x-5">
        <!-- Notifications -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="relative p-2.5 text-slate-400 hover:text-brand-600 hover:bg-brand-50 rounded-xl transition-all duration-300 group">
                <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                @php $unreadCount = Auth::user()->unreadNotifications->count(); @endphp
                @if($unreadCount > 0)
                <span class="absolute top-2.5 right-2.5 w-4 h-4 bg-brand-500 border-2 border-white rounded-full flex items-center justify-center text-[8px] text-white font-bold">{{ $unreadCount }}</span>
                @endif
            </button>

            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-3 w-80 bg-white rounded-3xl shadow-2xl border border-slate-100 py-4 z-50 overflow-hidden">
                <div class="px-6 pb-2 border-b border-slate-50 flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">Notifications</h3>
                    <span class="text-[10px] text-brand-600 font-bold bg-brand-50 px-2 py-0.5 rounded-full">{{ $unreadCount }} New</span>
                </div>
                <div class="max-h-96 overflow-y-auto pt-2">
                    @forelse(Auth::user()->notifications()->latest()->take(5)->get() as $notification)
                    <div class="px-6 py-4 hover:bg-slate-50 transition-colors cursor-pointer border-b border-slate-50">
                        <p class="text-[11px] font-bold text-slate-800">{{ $notification->data['message'] ?? 'New Notification' }}</p>
                        <p class="text-[9px] text-slate-400 mt-1 font-semibold uppercase tracking-tighter">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @empty
                    <div class="px-6 py-10 text-center">
                        <p class="text-xs text-slate-400 italic">No new notifications</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="h-6 w-px bg-slate-200 mx-1"></div>

        <!-- Professional Profile Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center space-x-3 p-1 rounded-full hover:bg-slate-100 transition-colors">
                <div class="w-9 h-9 rounded-full bg-brand-500 flex items-center justify-center text-white text-xs font-black shadow-lg shadow-brand-500/20">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open" @click.away="open = false" x-cloak 
                class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 z-50 animate-in fade-in slide-in-from-top-2 duration-200">
                <div class="px-5 py-3 border-b border-slate-50">
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Signed in as</p>
                    <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->email }}</p>
                </div>
                
                <a href="{{ route('settings') }}" class="flex items-center px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-brand-600 transition-colors">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Profile Settings
                </a>
                
                <div class="border-t border-slate-50 my-1"></div>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-5 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
