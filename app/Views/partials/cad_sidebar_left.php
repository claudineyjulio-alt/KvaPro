<aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col shrink-0 z-10">
    <!-- Project Tree -->
    <div class="h-1/3 border-b border-slate-800 flex flex-col">
        <div class="px-3 py-2 bg-slate-950 text-[10px] font-bold text-slate-500 uppercase tracking-wider flex justify-between items-center">
            Estrutura do Projeto
            <i data-lucide="folder-tree" class="w-3 h-3"></i>
        </div>
        <div class="flex-1 overflow-y-auto p-2">
            <div class="text-xs text-slate-300 font-medium mb-1 flex items-center gap-1">
                <i data-lucide="folder-open" class="w-3 h-3 text-blue-500"></i> Painel Principal 01
            </div>
            <div class="pl-4 border-l border-slate-700 ml-1.5 space-y-1 mt-1">
                <div class="flex items-center gap-2 text-xs text-white bg-blue-600/20 px-2 py-1 rounded cursor-pointer border border-blue-500/30">
                    <i data-lucide="file" class="w-3 h-3"></i> 01. Diagrama Principal
                </div>
            </div>
        </div>
    </div>

    <!-- Symbol Library -->
    <div class="flex-1 flex flex-col min-h-0">
        <div class="px-3 py-2 bg-slate-950 text-[10px] font-bold text-slate-500 uppercase tracking-wider flex justify-between items-center">
            Biblioteca
            <i data-lucide="search" class="w-3 h-3 cursor-pointer"></i>
        </div>
        
        <!-- Categories -->
        <div class="flex gap-1 p-2 border-b border-slate-800 overflow-x-auto no-scrollbar">
            <button class="bg-blue-600 text-white px-2 py-1 rounded text-[10px] whitespace-nowrap">IEC</button>
            <button class="bg-slate-800 text-slate-400 hover:text-white px-2 py-1 rounded text-[10px] whitespace-nowrap">NEMA</button>
        </div>
        
        <!-- Icons Grid -->
        <div class="flex-1 overflow-y-auto p-3 grid grid-cols-2 gap-2 align-start content-start">
            
            <?php 
                // Array global para JS (Será injetado no script principal)
                $jsLib = []; 
            ?>
            
            <?php if(isset($simbolosPorCategoria)): ?>
                <?php foreach ($simbolosPorCategoria as $categoria => $simbolos): ?>
                    <div class="col-span-2 mt-2 mb-1">
                        <span class="text-[10px] uppercase font-bold text-blue-500"><?= $categoria ?></span>
                    </div>

                    <?php foreach ($simbolos as $s): ?>
                        <?php 
                            $s['simbolo_svg'] = $s['simbolo_svg'] ?? '';
                            $jsLib[$s['id']] = $s; // Popula array para o JS
                        ?>
                        
                        <!-- Symbol Item -->
                        <div onclick="selecionarPeloID(<?= $s['id'] ?>)" 
                             class="bg-slate-800 p-2 rounded border border-slate-700 hover:border-blue-500 cursor-grab flex flex-col items-center gap-1 group transition-all hover:bg-slate-750">
                            
                            <div class="w-8 h-8 flex items-center justify-center opacity-70 group-hover:opacity-100">
                                <svg viewBox="0 0 100 100" class="stroke-slate-300 fill-none" style="stroke-width: 2;">
                                    <?= !empty($s['simbolo_svg']) ? preg_replace('/<\/?svg[^>]*>/i', '', $s['simbolo_svg']) : '' ?>
                                </svg>
                            </div>
                            
                            <span class="text-[9px] text-slate-500 group-hover:text-white text-center leading-tight">
                                <?= $s['nome'] ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
</aside>

<!-- Injeção da Biblioteca no JS Global -->
<script>
    window.biblioteca = <?= json_encode($jsLib ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
</script>