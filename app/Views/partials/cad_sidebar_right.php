<aside class="w-72 bg-slate-900 border-l border-slate-800 flex flex-col shrink-0 z-10">
    <!-- Header -->
    <div class="px-3 py-2 bg-slate-950 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-800">
        Propriedades
    </div>

    <!-- Empty State -->
    <div id="prop-empty" class="p-8 text-center mt-10">
        <i data-lucide="mouse-pointer-2" class="w-8 h-8 text-slate-700 mx-auto mb-2"></i>
        <p class="text-xs text-slate-600 italic">Selecione um componente<br>para ver detalhes.</p>
    </div>

    <!-- Active Form (Hidden by default) -->
    <div id="prop-form" class="hidden flex-1 overflow-y-auto p-4 space-y-4">
        <div class="flex items-center gap-3 border-b border-slate-800 pb-3">
            <div class="w-10 h-10 bg-slate-800 rounded flex items-center justify-center text-blue-500">
                <i data-lucide="settings-2" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 id="p-tag" class="text-sm font-bold text-white">---</h3>
                <p id="p-type" class="text-xs text-slate-500">---</p>
            </div>
        </div>

        <div class="space-y-3">
            <div>
                <label class="block text-[10px] uppercase text-slate-500 font-bold mb-1">Tag Visível</label>
                <input id="in-tag" type="text" class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-1 text-xs text-white focus:border-blue-500 focus:outline-none h-8">
            </div>
            
            <div id="field-manuf">
                <label class="block text-[10px] uppercase text-slate-500 font-bold mb-1">Fabricante</label>
                <div class="flex gap-2">
                     <input id="in-manuf" type="text" class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-1 text-xs text-white focus:border-blue-500 focus:outline-none h-8">
                     <button class="bg-slate-800 border border-slate-700 px-2 rounded hover:bg-slate-700 text-slate-400">...</button>
                </div>
            </div>
            
            <div class="border-t border-slate-800 pt-3 mt-4">
                <button onclick="aplicarPropriedades()" class="w-full bg-blue-600 hover:bg-blue-500 text-white py-2 rounded text-xs font-bold transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-3 h-3"></i> Aplicar Alterações
                </button>
            </div>
        </div>
    </div>
</aside>