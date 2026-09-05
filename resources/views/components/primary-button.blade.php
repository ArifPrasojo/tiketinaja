<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2.5 bg-electric-blue border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-electric-blue focus:ring-offset-2 active:scale-95 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
