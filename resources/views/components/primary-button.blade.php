<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-3 bg-slate-900 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wider hover:bg-slate-800 focus:bg-slate-800 active:bg-slate-950 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-all ease-in-out duration-200 shadow-md hover:shadow-lg']) }}>
    {{ $slot }}
</button>
