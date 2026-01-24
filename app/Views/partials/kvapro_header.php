<header class="h-10 bg-slate-900 border-b border-slate-800 flex items-center justify-between px-3 shrink-0 z-30">
    <div class="flex items-center gap-4">
        <!-- Logo Pequena -->
        <div class="flex items-center gap-2 mr-2">
            <img src="<?= base_url('assets/img/logo.png') ?>" class="h-5 w-auto" onerror="this.style.display='none'; document.getElementById('backup-icon').style.display='block'">
            <i id="backup-icon" data-lucide="zap" class="w-4 h-4 text-blue-500 hidden"></i>
            <span class="font-bold text-xs text-white tracking-wide">EletCAD <span class="text-slate-500 font-normal">Editor</span></span>
        </div>
        
        <!-- Menu Items -->
        <div class="flex gap-1">
            <button class="px-2 py-1 text-xs hover:bg-slate-800 rounded text-slate-300 transition-colors">Arquivo</button>
            <button class="px-2 py-1 text-xs hover:bg-slate-800 rounded text-slate-300 transition-colors">Editar</button>
            <button class="px-2 py-1 text-xs hover:bg-slate-800 rounded text-slate-300 transition-colors">Inserir</button>
            <button class="px-2 py-1 text-xs hover:bg-slate-800 rounded text-slate-300 transition-colors">Projeto</button>
            <button class="px-2 py-1 text-xs hover:bg-slate-800 rounded text-slate-300 transition-colors">Janela</button>
        </div>
    </div>

    <div class="flex items-center gap-3">
            <span class="text-[10px] text-slate-500 font-mono">Autosave: <?= date('H:i') ?></span>
            <a href="<?= base_url('dashboard') ?>" class="text-xs text-slate-500 hover:text-white transition-colors">Sair</a>
            <button class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1 rounded text-xs font-medium flex items-center gap-1 transition-colors">
                <i data-lucide="save" class="w-3 h-3"></i> Salvar
            </button>
    </div>
</header>