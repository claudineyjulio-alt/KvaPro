window.App = {
    stage: null,
    layers: { grid: null, main: null, ui: null, temp: null },
    state: {
        tool: 'cursor',
        lastDrawingTool: null,
        snapToGrid: true,
        gridSize: 10,
        scale: 1,
        osnap: true,
        ortho: false,
        osnapModes: {
            endpoint: true, midpoint: true, center: true, intersection: true, 
            nearest: true, quadrant: false, perpendicular: false, tangent: false
        }
    },
    history: [], historyStep: -1, isUndoing: false,

    init: function(containerId, width, height) {
        if(!document.getElementById(containerId)) return;
        
        this.stage = new Konva.Stage({ container: containerId, width: width, height: height, draggable: false });
        
        this.layers.grid = new Konva.Layer({name: 'grid', listening: false});
        this.layers.main = new Konva.Layer({name: 'main'});
        this.layers.temp = new Konva.Layer({name: 'temp', listening: false}); 
        this.layers.ui = new Konva.Layer({name: 'ui'}); 

        this.stage.add(this.layers.grid);
        this.stage.add(this.layers.main);
        this.stage.add(this.layers.temp);
        this.stage.add(this.layers.ui);
        
        this.saveState("Início");

        this.stage.on('wheel', (e) => this.handleZoom(e));
        this.stage.on('mousedown', (e) => {
            if (e.evt.button === 1) { 
                e.evt.preventDefault();
                this.stage.draggable(true);
                this.stage.container().style.cursor = 'grabbing';
            }
        });
        
        this.stage.on('mouseup', (e) => {
            if (e.evt.button === 1) {
                this.stage.draggable(false);
                this.updateCursor();
            }
        });

        window.addEventListener('keydown', (e) => {
            if (e.key === 'F8') { e.preventDefault(); this.toggleOrtho(); }
            if (e.key === 'F9') { e.preventDefault(); this.toggleSnap(); }
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z') { e.preventDefault(); this.undo(); }
            if ((e.ctrlKey || e.metaKey) && (e.key.toLowerCase() === 'y' || (e.shiftKey && e.key.toLowerCase() === 'z'))) { e.preventDefault(); this.redo(); }
        });
        console.log("EletCAD Core v7.1 (Move Workflow)");
    },
    
    setTool: function(toolName) {
        const drawingTools = ['line', 'rect', 'circle', 'borne', 'polyline', 'polygon', 'text'];
        if (drawingTools.includes(toolName)) this.state.lastDrawingTool = toolName;
        this.state.tool = toolName;
        
        document.querySelectorAll('.tool-btn').forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white');
            btn.classList.add('text-slate-300', 'hover:bg-slate-700');
        });
        const activeBtn = document.getElementById('btn-' + toolName);
        if(activeBtn) {
            activeBtn.classList.remove('text-slate-300', 'hover:bg-slate-700');
            activeBtn.classList.add('bg-blue-600', 'text-white');
        }

        this.updateCursor();
        this.layers.temp.destroyChildren(); 
        this.layers.temp.batchDraw();

        if(window.Editor) {
            window.Editor.resetToolState();
            // CORREÇÃO: Não limpa seleção se for Mover
            if(toolName !== 'cursor' && toolName !== 'move') window.Editor.clearSelection();
        }
        
        // Mensagem Inicial Inteligente
        let msg = "Pronto.";
        if(drawingTools.includes(toolName)) msg = "Especifique o primeiro ponto";
        
        // Verifica se a ferramenta tem inicialização própria (ex: Move verificando seleção)
        if(window.DrawingTools && window.DrawingTools[toolName] && window.DrawingTools[toolName].init) {
             const initResult = window.DrawingTools[toolName].init();
             if(initResult && initResult.msg) msg = initResult.msg;
        } else if (toolName === 'move') {
             // Fallback caso init não exista
             msg = "Selecione objetos e tecle Enter";
        }
        
        this.setPrompt(msg);
    },
    
    // ... (Restante mantido idêntico) ...
    updateCursor: function() { const tool = this.state.tool; const container = this.stage.container(); if (tool === 'pan') container.style.cursor = 'grab'; else if (['line', 'rect', 'circle', 'borne', 'polyline', 'polygon', 'text'].includes(tool)) container.style.cursor = 'crosshair'; else if (tool === 'move') container.style.cursor = 'default'; else container.style.cursor = 'default'; },
    setPrompt: function(msg) { const el = document.getElementById('footer-msg'); if(el) { el.innerText = msg; el.style.display = msg ? 'inline-block' : 'none'; } },
    toggleOrtho: function() { this.state.ortho = !this.state.ortho; const btn = document.getElementById('footer-ortho'); if(btn) { btn.innerHTML = this.state.ortho ? 'ORTHO: <span class="text-green-500 font-bold">ON</span>' : 'ORTHO: <span class="text-slate-600">OFF</span>'; btn.classList.toggle('text-white', this.state.ortho); } },
    toggleSnap: function() { this.state.snapToGrid = !this.state.snapToGrid; const btn = document.getElementById('footer-snap'); if(btn) btn.innerHTML = this.state.snapToGrid ? 'SNAP: <span class="text-green-500 font-bold">ON</span>' : 'SNAP: <span class="text-red-500">OFF</span>'; },
    toggleOsnapMode: function(mode) { if(this.state.osnapModes.hasOwnProperty(mode)) { this.state.osnapModes[mode] = !this.state.osnapModes[mode]; } },
    applyOrthoConstraints: function(start, current) { if (!this.state.ortho) return current; const dx = Math.abs(current.x - start.x); const dy = Math.abs(current.y - start.y); return (dx > dy) ? { x: current.x, y: start.y } : { x: start.x, y: current.y }; },
    saveState: function(actionName="Ação") { if(this.isUndoing) return; if (this.historyStep < this.history.length-1) this.history = this.history.slice(0, this.historyStep+1); const state = JSON.parse(this.layers.main.toJSON()); this.history.push({state:state, name:actionName}); this.historyStep++; this.logHistory(); },
    undo: function() { if (this.historyStep > 0) { this.isUndoing=true; this.historyStep--; this.loadHistoryState(this.history[this.historyStep].state); this.isUndoing=false; this.logHistory(); } },
    redo: function() { if (this.historyStep < this.history.length-1) { this.isUndoing=true; this.historyStep++; this.loadHistoryState(this.history[this.historyStep].state); this.isUndoing=false; this.logHistory(); } },
    loadHistoryState: function(stateObj) { if(window.Editor) window.Editor.clearSelection(); this.layers.main.destroy(); this.layers.main = Konva.Node.create(stateObj); this.stage.add(this.layers.main); this.layers.main.zIndex(1); this.stage.batchDraw(); },
    logHistory: function() { const log = document.getElementById('debug-log'); if(!log) return; let html = ''; this.history.forEach((h, i) => { const isCurrent = i === this.historyStep ? 'bg-slate-800 text-green-400 font-bold border-l-2 border-green-500' : 'text-slate-500 hover:bg-slate-800/50'; const marker = i === this.historyStep ? '➜' : (i > this.historyStep ? ' ' : '✓'); html += `<div id="log-item-${i}" class="entry text-xs py-1 px-2 mb-1 rounded cursor-pointer ${isCurrent}"><span class="inline-block w-4 text-center">${marker}</span> <span class="opacity-50 text-[10px] mr-1">[${i}]</span> ${h.name}</div>`; }); log.innerHTML = html; const currentItem = document.getElementById(`log-item-${this.historyStep}`); if(currentItem) currentItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); },
    getPointerPos: function() { if(!this.layers.main.getStage()) return {x:0, y:0}; const transform = this.layers.main.getAbsoluteTransform().copy().invert(); const pos = this.stage.getPointerPosition(); if(!pos) return {x:0, y:0}; return transform.point(pos); },
    getSnappedPos: function(pos) { if (!this.state.snapToGrid) return pos; const s = this.state.gridSize; return { x: Math.round(pos.x / s) * s, y: Math.round(pos.y / s) * s }; },
    handleZoom: function(e) { e.evt.preventDefault(); const scaleBy = 1.1; const oldScale = this.stage.scaleX(); const pointer = this.stage.getPointerPosition(); const mousePointTo = { x: (pointer.x - this.stage.x()) / oldScale, y: (pointer.y - this.stage.y()) / oldScale }; const newScale = e.evt.deltaY > 0 ? oldScale / scaleBy : oldScale * scaleBy; if (newScale < 0.1 || newScale > 10) return; this.stage.scale({ x: newScale, y: newScale }); const newPos = { x: pointer.x - mousePointTo.x * newScale, y: pointer.y - mousePointTo.y * newScale }; this.stage.position(newPos); this.state.scale = newScale; const zl = document.getElementById('zoom-level'); if(zl) zl.innerText = Math.round(newScale * 100) + '%'; },
    zoom: function(amount) { const current = this.stage.scaleX(); const next = current + amount; if(next > 0.1 && next < 10) { this.stage.scale({x: next, y: next}); this.state.scale = next; document.getElementById('zoom-level').innerText = Math.round(next * 100) + '%'; } },
    toggleSnap: function() { this.state.snapToGrid = !this.state.snapToGrid; const btn = document.getElementById('footer-snap'); if(btn) btn.innerHTML = this.state.snapToGrid ? 'SNAP: <span class="text-green-500 font-bold">ON</span>' : 'SNAP: <span class="text-red-500">OFF</span>'; },
    togglePolygonMenu: function() { let sides = prompt("Número de lados:", "6"); if(sides) { sides = parseInt(sides); if(sides < 3) sides = 3; if(window.DrawingTools && window.DrawingTools.polygon) { window.DrawingTools.polygon.sides = sides; this.setTool('polygon'); } } }
};