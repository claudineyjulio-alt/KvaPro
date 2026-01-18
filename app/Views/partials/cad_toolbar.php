<div class="h-12 bg-slate-800/50 border-b border-slate-800 flex items-center px-4 gap-2 shrink-0 z-20 select-none">
    
    <!-- Histórico -->
    <div class="flex gap-1 mr-2">
        <button onclick="App.undo()" class="p-1.5 hover:bg-slate-700 rounded text-slate-400 hover:text-white transition-colors" title="Desfazer (Ctrl+Z)"><i data-lucide="undo-2" class="w-4 h-4"></i></button>
        <button onclick="App.redo()" class="p-1.5 hover:bg-slate-700 rounded text-slate-400 hover:text-white transition-colors" title="Refazer (Ctrl+Y)"><i data-lucide="redo-2" class="w-4 h-4"></i></button>
    </div>
    <div class="w-px h-6 bg-slate-700 mx-1"></div>

    <!-- Ponteiros -->
    <div class="flex bg-slate-900 p-1 rounded-md border border-slate-700">
        <button onclick="App.setTool('cursor')" id="btn-cursor" class="tool-btn p-1.5 rounded bg-blue-600 text-white shadow-sm transition-colors" title="Selecionar (V)"><i data-lucide="mouse-pointer-2" class="w-4 h-4"></i></button>
        <button onclick="App.setTool('pan')" id="btn-pan" class="tool-btn p-1.5 rounded hover:bg-slate-800 text-slate-400 transition-colors" title="Pan (H)"><i data-lucide="hand" class="w-4 h-4"></i></button>
    </div>

        <!-- Modificação -->
    <div class="flex gap-1 ml-1">
        <button onclick="App.setTool('move')" id="btn-move" class="tool-btn p-1.5 hover:bg-slate-700 rounded text-slate-300" title="Mover (M)">
            <i data-lucide="move" class="w-4 h-4"></i>
        </button>
        <button onclick="App.setTool('trim')" id="btn-trim" class="tool-btn p-1.5 hover:bg-slate-700 rounded text-slate-300" title="Trim/Aparar (TR)">
            <i data-lucide="scissors" class="w-4 h-4"></i>
        </button>
    </div>

    <div class="w-px h-6 bg-slate-700 mx-1"></div>

    <!-- Desenho -->
    <div class="flex gap-1">
        <button onclick="App.setTool('line')" id="btn-line" class="tool-btn p-1.5 hover:bg-slate-700 rounded text-slate-300" title="Linha (L)"><i data-lucide="minus" class="w-4 h-4 -rotate-45"></i></button>
        <!-- Ícone Activity para Polilinha (Segmentos) -->
        <button onclick="App.setTool('polyline')" id="btn-polyline" class="tool-btn p-1.5 hover:bg-slate-700 rounded text-slate-300" title="Polilinha"><i data-lucide="activity" class="w-4 h-4"></i></button>
        <button onclick="App.setTool('rect')" id="btn-rect" class="tool-btn p-1.5 hover:bg-slate-700 rounded text-slate-300" title="Retângulo"><i data-lucide="square" class="w-4 h-4"></i></button>
        <button onclick="App.setTool('circle')" id="btn-circle" class="tool-btn p-1.5 hover:bg-slate-700 rounded text-slate-300" title="Círculo"><i data-lucide="circle" class="w-4 h-4"></i></button>
        <!-- Ícone Pentagon para Polígono -->
        <button onclick="App.togglePolygonMenu()" id="btn-polygon" class="tool-btn p-1.5 hover:bg-slate-700 rounded text-slate-300" title="Polígono"><i data-lucide="pentagon" class="w-4 h-4"></i></button>
        <button onclick="App.setTool('text')" id="btn-text" class="tool-btn p-1.5 hover:bg-slate-700 rounded text-slate-300" title="Texto"><i data-lucide="type" class="w-4 h-4"></i></button>
    </div>

    <!-- Específico Símbolo/Diagrama -->
    <div class="w-px h-6 bg-slate-700 mx-1"></div>
    <?php if(isset($modo) && $modo == 'simbolo'): ?>
        <div class="flex gap-1">
            <button onclick="App.setTool('borne')" id="btn-borne" class="tool-btn p-1.5 hover:bg-slate-700 rounded text-red-300" title="Terminal"><i data-lucide="crosshair" class="w-4 h-4"></i></button>
        </div>
    <?php else: ?>
        <div class="flex gap-1">
            <button onclick="App.setTool('cabo')" id="btn-cabo" class="tool-btn p-1.5 hover:bg-slate-700 rounded text-green-300" title="Cabo"><i data-lucide="activity" class="w-4 h-4"></i></button>
        </div>
    <?php endif; ?>

    <div class="flex-1"></div>

    <!-- Auxiliares -->
    <div class="flex items-center gap-2">
        <button onclick="App.toggleOrtho()" id="btn-ortho" class="text-xs font-mono text-slate-600 hover:text-white transition-colors" title="Ortogonal (F8)">ORTHO: OFF</button>
        <div class="h-4 w-px bg-slate-700"></div>

        <!-- OSNAP Dropdown -->
        <div class="relative mr-2">
            <button onclick="document.getElementById('osnap-menu').classList.toggle('hidden')" class="flex items-center gap-1 text-xs font-mono bg-slate-900 border border-slate-700 px-2 py-1 rounded hover:bg-slate-800 text-green-400">
                <i data-lucide="magnet" class="w-3 h-3"></i> OSNAP <i data-lucide="chevron-down" class="w-3 h-3 ml-1"></i>
            </button>
            <div id="osnap-menu" class="hidden absolute top-full right-0 mt-2 w-48 bg-slate-800 border border-slate-600 rounded shadow-xl p-2 z-50 grid grid-cols-1 gap-1" onmouseleave="this.classList.add('hidden')">
                <label class="flex items-center gap-2 px-2 py-1 hover:bg-slate-700 rounded cursor-pointer"><input type="checkbox" checked onchange="App.toggleOsnapMode('endpoint')"> <span class="text-xs text-white">Ponto Final</span></label>
                <label class="flex items-center gap-2 px-2 py-1 hover:bg-slate-700 rounded cursor-pointer"><input type="checkbox" checked onchange="App.toggleOsnapMode('midpoint')"> <span class="text-xs text-white">Ponto Médio</span></label>
                <label class="flex items-center gap-2 px-2 py-1 hover:bg-slate-700 rounded cursor-pointer"><input type="checkbox" checked onchange="App.toggleOsnapMode('center')"> <span class="text-xs text-white">Centro</span></label>
                <label class="flex items-center gap-2 px-2 py-1 hover:bg-slate-700 rounded cursor-pointer"><input type="checkbox" checked onchange="App.toggleOsnapMode('intersection')"> <span class="text-xs text-white">Intersecção</span></label>
                <label class="flex items-center gap-2 px-2 py-1 hover:bg-slate-700 rounded cursor-pointer"><input type="checkbox" checked onchange="App.toggleOsnapMode('nearest')"> <span class="text-xs text-white">Mais Próximo</span></label>
                <label class="flex items-center gap-2 px-2 py-1 hover:bg-slate-700 rounded cursor-pointer"><input type="checkbox" onchange="App.toggleOsnapMode('quadrant')"> <span class="text-xs text-white">Quadrante</span></label>
                <hr class="border-slate-600 my-1">
                <label class="flex items-center gap-2 px-2 py-1 hover:bg-slate-700 rounded cursor-pointer"><input type="checkbox" onchange="App.toggleOsnapMode('perpendicular')"> <span class="text-xs text-slate-400">Perpendicular</span></label>
                <label class="flex items-center gap-2 px-2 py-1 hover:bg-slate-700 rounded cursor-pointer"><input type="checkbox" onchange="App.toggleOsnapMode('tangent')"> <span class="text-xs text-slate-400">Tangente</span></label>
            </div>
        </div>

        <div class="h-4 w-px bg-slate-700"></div>

        <div class="flex items-center bg-slate-900 rounded-md border border-slate-700 px-2 py-1">
            <button onclick="App.zoom(-0.1)" class="text-slate-400 hover:text-white"><i data-lucide="minus" class="w-3 h-3"></i></button>
            <span id="zoom-level" class="mx-2 text-xs font-mono w-10 text-center">100%</span>
            <button onclick="App.zoom(0.1)" class="text-slate-400 hover:text-white"><i data-lucide="plus" class="w-3 h-3"></i></button>
        </div>
    </div>
</div>