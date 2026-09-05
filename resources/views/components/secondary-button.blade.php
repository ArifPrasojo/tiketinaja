<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2.5 bg-white border-2 border-stadium-gray rounded-lg font-bold text-xs text-pitch-black uppercase tracking-widest shadow-sm hover:border-electric-blue hover:text-electric-blue focus:outline-none focus:ring-2 focus:ring-electric-blue focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
