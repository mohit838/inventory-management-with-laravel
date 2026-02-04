<div x-data="{ 
    init() {
        const Toast = Swal.mixin({
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            customClass: {
                popup: 'rounded-3xl border border-slate-100 shadow-2xl p-4',
                title: 'text-sm font-black text-slate-800 tracking-tight',
            },
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        // Handle Laravel Session Flash
        @if(session('success'))
            Toast.fire({ icon: 'success', title: '{{ session('success') }}' });
        @elseif(session('error'))
            Toast.fire({ icon: 'error', title: '{{ session('error') }}' });
        @elseif(session('status'))
            Toast.fire({ icon: 'info', title: '{{ session('status') }}' });
        @endif

        // Dynamic JS Bridge
        window.addEventListener('notify', (event) => {
            Toast.fire({
                icon: event.detail.type || 'success',
                title: event.detail.message
            });
        });

        // Global Confirmation Script
        window.confirmAction = (options = {}) => {
            return Swal.fire({
                title: options.title || 'Are you sure?',
                text: options.text || 'This action cannot be undone.',
                icon: options.icon || 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0ea5e9', // Brand 500
                cancelButtonColor: '#f43f5e',   // Rose 500
                confirmButtonText: options.confirmButtonText || 'Confirm Action',
                padding: '2rem',
                customClass: {
                    popup: 'rounded-[2.5rem] border-none shadow-2xl',
                    title: 'text-2xl font-black text-slate-800 tracking-tighter',
                    htmlContainer: 'text-slate-500 font-medium text-sm',
                    confirmButton: 'rounded-2xl px-8 py-4 font-black uppercase tracking-widest text-[10px]',
                    cancelButton: 'rounded-2xl px-8 py-4 font-black uppercase tracking-widest text-[10px]'
                }
            });
        }
    }
}"></div>
