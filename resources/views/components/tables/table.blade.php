<div class="w-full overflow-auto rounded-xl border border-slate-200/60 bg-white shadow-sm">
    <table {{ $attributes->merge(['class' => 'w-full text-sm text-left']) }}>
        {{ $slot }}
    </table>
</div>
