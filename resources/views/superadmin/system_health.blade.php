@extends('layouts.app')

@section('header', 'System Diagnostics')

@section('content')
<div class="space-y-10">
    <!-- Hardware & Database Health -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 flex flex-col justify-between group hover:border-brand-100 transition-all">
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center shadow-inner shrink-0 text-xl font-black">CPU</div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Load Average</span>
            </div>
            <div>
                <p class="text-4xl font-black text-slate-800 tracking-tight leading-none">{{ $cpuLoad[0] }}</p>
                <div class="mt-4 h-1.5 w-full bg-slate-50 rounded-full overflow-hidden">
                    <div class="h-full bg-brand-500 rounded-full transition-all duration-1000" style="width: {{ min(($cpuLoad[0] / 8) * 100, 100) }}%"></div>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 flex flex-col justify-between group hover:border-emerald-100 transition-all">
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center shadow-inner shrink-0 text-xl font-black">RAM</div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Utilization: {{ $memInfo['used_percent'] }}%</span>
            </div>
            <div>
                <p class="text-4xl font-black text-slate-800 tracking-tight leading-none">{{ $memInfo['total'] }}</p>
                <p class="text-[10px] text-slate-400 font-bold uppercase mt-2 tracking-widest">{{ $memInfo['free'] }} Available</p>
                <div class="mt-4 h-1.5 w-full bg-slate-50 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000" style="width: {{ $memInfo['used_percent'] }}%"></div>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 flex flex-col justify-between group hover:border-indigo-100 transition-all">
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-inner shrink-0 text-xl font-black">DB</div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Storage Footprint</span>
            </div>
            <div>
                <p class="text-4xl font-black text-slate-800 tracking-tight leading-none">{{ $dbSize }}</p>
                <p class="text-[10px] text-slate-400 font-bold uppercase mt-2 tracking-widest">High-Performance Querying Active</p>
                <div class="mt-4 h-1.5 w-full bg-slate-50 rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-500 rounded-full" style="width: 25%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Culprit Table: Slow Endpoints -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200/60 overflow-hidden">
        <div class="p-10 border-b border-slate-50 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-black text-slate-800 tracking-tight">Performance Culprits (Slow API Endpoints)</h3>
                <p class="text-slate-400 text-[10px] font-black mt-1 uppercase tracking-widest">Identifying requests exceeding 500ms latency</p>
            </div>
            <span class="px-5 py-2 bg-rose-50 text-rose-600 text-[10px] font-black uppercase rounded-full border border-rose-100">Observability Mode Active</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Endpoint Path</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Latency</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Last Triggered</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($slowEndpoints as $ep)
                    <tr class="hover:bg-rose-50/10 transition-colors group">
                        <td class="px-10 py-6">
                            <div class="flex items-center space-x-3">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[9px] font-black rounded uppercase">{{ $ep['method'] }}</span>
                                <p class="text-sm font-black text-slate-700 tracking-tight">{{ $ep['uri'] }}</p>
                            </div>
                        </td>
                        <td class="px-10 py-6 text-center">
                            <span class="text-xs font-black {{ floatval($ep['duration']) > 1.5 ? 'text-rose-600' : 'text-orange-500' }}">{{ $ep['duration'] }}</span>
                        </td>
                        <td class="px-10 py-6 text-right">
                            <span class="text-[11px] font-bold text-slate-400 uppercase">{{ $ep['date'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Error Logs -->
    <div class="bg-slate-950 p-10 rounded-[2.5rem] shadow-2xl border border-slate-800/40 relative overflow-hidden">
        <!-- Abstract gradient decor -->
        <div class="absolute -right-40 -top-40 w-96 h-96 bg-brand-500/10 rounded-full blur-[100px]"></div>
        
        <div class="flex items-center justify-between mb-8 relative z-10">
            <h3 class="text-lg font-black text-white tracking-tight">System Fault Manifest (Last 5 Criticals)</h3>
            <div class="flex space-x-2">
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                <span class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em]">Monitoring storage/logs/laravel.log</span>
            </div>
        </div>
        
        <div class="space-y-4 relative z-10">
            @forelse($errorLogs as $log)
            <div class="p-6 bg-slate-900/60 border border-slate-800/50 rounded-2xl group hover:border-rose-500/30 transition-all">
                <div class="flex items-start">
                    <div class="w-2 h-2 bg-rose-500 rounded-full mt-1.5 shrink-0 mr-4"></div>
                    <pre class="text-[11px] font-medium text-slate-300 whitespace-pre-wrap break-all font-mono leading-relaxed">{{ trim($log) }}</pre>
                </div>
            </div>
            @empty
            <div class="py-10 text-center flex flex-col items-center">
                <div class="w-12 h-12 bg-emerald-500/10 text-emerald-500 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="text-sm font-black text-slate-500 tracking-tight uppercase">Infrastructure Stabilized: No errors found</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
