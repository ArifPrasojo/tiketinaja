@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-white border-2 border-stadium-gray text-pitch-black placeholder:text-slate-400 focus:border-electric-blue focus:ring-0 rounded-lg shadow-sm']) }}>
