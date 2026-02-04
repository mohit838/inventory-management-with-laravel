<div x-data="{ 
    show: false, 
    message: '', 
    type: 'success',
    icon: '',
    init() {
        @if(session('success'))
            this.notify('{{ session('success') }}', 'success');
        @elseif(session('error'))
            this.notify('{{ session('error') }}', 'error');
        @elseif(session('status'))
            this.notify('{{ session('status') }}', 'info');
        @endif

        window.addEventListener('notify', (event) => {
            this.notify(event.detail.message, event.detail.type || 'success', event.detail.icon || '');
        });
    },
    notify(msg, type, icon = '') {
        this.message = msg;
        this.type = type;
        this.icon = icon;
        this.show = true;
        setTimeout(() => { this.show = false }, 5000);
    }
}" 
x-show="show" 
x-transition:enter="transition ease-out duration-500"
x-transition:enter-start="translate-y-12 opacity-0 scale-90"
x-transition:enter-end="translate-y-0 opacity-100 scale-100"
x-transition:leave="transition ease-in duration-300"
x-transition:leave-start="opacity-100 scale-100"
x-transition:leave-end="opacity-0 scale-90"
class="fixed bottom-10 right-10 z-[100] max-w-sm w-full pointer-events-none"
x-cloak>
    <div :class="{
        'bg-white border-emerald-50 text-emerald-900 shadow-emerald-500/10': type === 'success',
        'bg-white border-rose-50 text-rose-900 shadow-rose-500/10': type === 'error',
        'bg-white border-brand-50 text-brand-900 shadow-brand-500/10': type === 'info'
    }" class="p-5 rounded-3xl border shadow-2xl pointer-events-auto flex items-center group relative overflow-hidden">
        
        <!-- Animated Background Decor -->
        <div :class="{
            'bg-emerald-500/5': type === 'success',
            'bg-rose-500/5': type === 'error',
            'bg-brand-500/5': type === 'info'
        }" class="absolute inset-0 -translate-x-full group-hover:translate-x-0 transition-transform duration-700 ease-out"></div>

        <div :class="{
            'bg-emerald-100 text-emerald-600': type === 'success',
            'bg-rose-100 text-rose-600': type === 'error',
            'bg-brand-100 text-brand-600': type === 'info'
        }" class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 mr-4 shadow-inner relative z-10">
            <template x-if="!icon">
                <template x-if="type === 'success'">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                </template>
                <template x-if="type === 'error'">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                </template>
                <template x-if="type === 'info'">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                </template>
            </template>
            <template x-if="icon">
                <span x-html="icon" class="w-6 h-6"></span>
            </template>
        </div>

        <div class="flex-1 relative z-10">
            <h4 class="text-xs font-black uppercase tracking-widest opacity-40 mb-1" x-text="type === 'success' ? 'Success' : (type === 'error' ? 'Error' : 'Notification')"></h4>
            <p class="text-sm font-bold leading-tight" x-text="message"></p>
        </div>

        <button @click="show = false" class="ml-4 text-slate-300 hover:text-slate-500 transition-colors relative z-10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
</div>
