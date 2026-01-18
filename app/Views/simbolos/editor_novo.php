<?= $this->extend('layouts/editor_layout') ?>

<?= $this->section('styles') ?>
<style>
    .cad-grid {
        background-color: #0f172a; 
        background-size: 40px 40px;
        background-image:
            linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
    }
    
    #dynamic-input {
        backdrop-filter: blur(4px);
    }
    
    /* Painel de Histórico Restaurado */
    #debug-panel {
        position: fixed;
        bottom: 35px; /* Acima da barra de status */
        right: 10px;
        width: 220px;
        height: 180px;
        background: rgba(15, 23, 42, 0.95);
        border: 1px solid #334155;
        color: #94a3b8;
        font-family: 'JetBrains Mono', monospace;
        z-index: 1000;
        border-radius: 6px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 15px rgba(0,0,0,0.5);
    }
    
    #debug-log {
        flex: 1;
        overflow-y: auto;
        padding: 5px;
    }
    
    /* Scrollbar fina */
    #debug-log::-webkit-scrollbar { width: 4px; }
    #debug-log::-webkit-scrollbar-thumb { background: #475569; border-radius: 2px; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div id="container" class="absolute inset-0 cad-grid" oncontextmenu="return false;"></div>
    
    <!-- Painel de Histórico -->
    <div id="debug-panel">
        <div class="font-bold text-[10px] text-slate-500 border-b border-slate-700 p-2 bg-slate-900 rounded-t flex justify-between items-center">
            <span>HISTÓRICO</span>
            <i data-lucide="history" class="w-3 h-3"></i>
        </div>
        <div id="debug-log"></div>
    </div>

    <!-- Barra de Status Inferior -->
    <div class="absolute bottom-0 left-0 right-0 h-7 bg-slate-900 border-t border-slate-800 flex items-center px-4 text-[11px] text-slate-400 justify-between select-none pointer-events-none z-20">
        <div class="flex items-center gap-6 font-mono pointer-events-auto">
            <span id="footer-coords" class="text-slate-300 min-w-[120px]">X: 0.00, Y: 0.00</span>
            
            <span id="footer-msg" class="text-yellow-400 font-bold bg-slate-800 px-2 py-0.5 rounded border border-slate-700" style="display:none">
                Pronto.
            </span>
        </div>
        
        <div class="flex items-center gap-4 font-mono pointer-events-auto">
            <span id="footer-snap" onclick="App.toggleSnap()" class="cursor-pointer hover:text-white transition-colors" title="F9">
                SNAP: <span class="text-green-500 font-bold">ON</span>
            </span>
            <span class="text-slate-500">|</span>
            <span id="footer-ortho" onclick="App.toggleOrtho()" class="cursor-pointer hover:text-white transition-colors" title="F8">
                ORTHO: <span class="text-slate-600">OFF</span>
            </span>
            <span class="text-slate-500">|</span>
            <span class="text-slate-500 cursor-default">INPUT: REL</span>
            <span class="text-slate-500">|</span>
            <span>OSNAP</span>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('js/cad_utils.js') ?>"></script>
    <script src="<?= base_url('js/cad_core.js') ?>"></script>
    <script src="<?= base_url('js/drawing_tools.js') ?>"></script>
    <script src="<?= base_url('js/symbol_editor.js') ?>"></script>
<?= $this->endSection() ?>