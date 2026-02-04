@extends('layouts.app')

@section('content')
<div class="px-8 py-8">
    <div class="flex justify-between items-end mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Onboarding Requests</h2>
            <p class="text-slate-500 font-medium mt-1">Manage prospective organization owners and enterprise partnerships.</p>
        </div>
        <div class="flex space-x-3">
            <span class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-black uppercase rounded-xl border border-slate-200">
                Pending: {{ $totalPending }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-sm font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-bottom border-slate-100">
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Requester / Organization</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Requested On</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($requests as $request)
                <tr class="hover:bg-slate-50/30 transition-colors group">
                    <td class="px-8 py-6">
                        <p class="text-sm font-black text-slate-800 tracking-tight group-hover:text-brand-600 transition-colors">{{ $request->email }}</p>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mt-0.5">{{ $request->organization_name }}</p>
                    </td>
                    <td class="px-8 py-6 text-center">
                        @if($request->status === 'pending')
                            <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-black uppercase rounded-full border border-amber-100 leading-none">Awaiting Review</span>
                        @elseif($request->status === 'accepted')
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase rounded-full border border-emerald-100 leading-none">Invitation Sent</span>
                        @else
                            <span class="px-3 py-1 bg-slate-100 text-slate-500 text-[10px] font-black uppercase rounded-full border border-slate-200 leading-none">Rejected</span>
                        @endif
                    </td>
                    <td class="px-8 py-6">
                        <p class="text-xs font-bold text-slate-500">{{ $request->created_at->format('M d, Y') }}</p>
                        <p class="text-[9px] text-slate-400 uppercase font-black tracking-widest">{{ $request->created_at->diffForHumans() }}</p>
                    </td>
                    <td class="px-8 py-6 text-right">
                        @if($request->status === 'pending')
                        <div class="flex justify-end space-x-2">
                            <form action="{{ route('superadmin.requests.approve', $request) }}" method="POST">
                                @csrf
                                <button class="p-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-xl transition-all shadow-sm border border-emerald-100 flex items-center px-4 space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    <span class="text-[10px] font-black uppercase tracking-widest">Approve & Invite</span>
                                </button>
                            </form>
                            <form action="{{ route('superadmin.requests.reject', $request) }}" method="POST">
                                @csrf
                                <button class="p-2 bg-slate-50 text-slate-400 hover:bg-slate-900 hover:text-white rounded-xl transition-all border border-slate-100 shadow-sm flex items-center px-4 space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    <span class="text-[10px] font-black uppercase tracking-widest">Reject</span>
                                </button>
                            </form>
                        </div>
                        @else
                            <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest italic">Processed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <div class="p-4 bg-slate-50 rounded-full mb-4">
                                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            </div>
                            <p class="text-slate-400 font-bold">No onboarding requests found.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-6 bg-slate-50/50 border-t border-slate-100">
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection
