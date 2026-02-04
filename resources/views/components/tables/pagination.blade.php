<div class="flex items-center justify-between px-6 py-4 bg-white border-t border-slate-100 rounded-b-3xl">
    <div class="flex flex-1 justify-between sm:hidden">
        @if ($paginator->onFirstPage())
            <span class="relative inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-400 cursor-default uppercase tracking-widest">Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors uppercase tracking-widest">Previous</a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="relative ml-3 inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors uppercase tracking-widest">Next</a>
        @else
            <span class="relative ml-3 inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-400 cursor-default uppercase tracking-widest">Next</span>
        @endif
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Rows per page</span>
                <div class="relative">
                    <select class="bg-slate-50 border border-slate-200 text-slate-700 text-[11px] font-black rounded-xl focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 block pl-4 pr-10 py-1.5 appearance-none cursor-pointer transition-all">
                        <option value="10" {{ $paginator->perPage() == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $paginator->perPage() == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $paginator->perPage() == 50 ? 'selected' : '' }}>50</option>
                    </select>
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <nav class="isolate inline-flex flex-wrap lg:flex-nowrap items-center space-x-1" aria-label="Pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="relative inline-flex items-center rounded-full px-3 py-2 text-slate-300 transition-colors cursor-default">
                        <svg class="h-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" /></svg>
                        <span class="ml-1 text-[10px] font-black uppercase tracking-widest hidden lg:block">Previous</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center rounded-full px-3 py-2 text-slate-600 hover:bg-slate-100 transition-colors group">
                        <svg class="h-4 w-4 text-slate-400 group-hover:text-brand-600 transition-colors" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" /></svg>
                        <span class="ml-1 text-[10px] font-black uppercase tracking-widest hidden lg:block">Previous</span>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="relative inline-flex items-center px-4 py-2 text-xs font-black text-slate-300 cursor-default">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="relative z-10 inline-flex items-center bg-brand-600 px-4 py-2 text-[11px] font-black text-white rounded-xl shadow-md shadow-brand-200 ring-2 ring-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 text-[11px] font-bold text-slate-500 hover:text-brand-600 hover:bg-brand-50/50 rounded-xl transition-all">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center rounded-full px-3 py-2 text-slate-600 hover:bg-slate-100 transition-colors group">
                        <span class="mr-1 text-[10px] font-black uppercase tracking-widest hidden lg:block">Next</span>
                        <svg class="h-4 w-4 text-slate-400 group-hover:text-brand-600 transition-colors" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
                    </a>
                @else
                    <span class="relative inline-flex items-center rounded-full px-3 py-2 text-slate-300 transition-colors cursor-default">
                        <span class="mr-1 text-[10px] font-black uppercase tracking-widest hidden lg:block">Next</span>
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
                    </span>
                @endif
            </nav>
        </div>
    </div>
</div>
