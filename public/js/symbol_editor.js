window.Editor = {
    selectionRect: null, selectionStart: null, selectedNodes: [],
    originalStyles: new Map(), transformer: null,
    cmdInput: null, relCoordsTooltip: null, commandBuffer: '',
    hasUnsavedChanges: false, osnapGroup: null, 

    init: function() {
        const container = document.getElementById('workspace');
        if(!container) return;
        App.init('container', container.offsetWidth, container.offsetHeight);
        const stage = App.stage;
        
        stage.on('mousedown', (e) => this.onMouseDown(e));
        stage.on('mousemove', (e) => this.onMouseMove(e));
        stage.on('mouseup', (e) => this.onMouseUp(e));
        stage.on('dragend', (e) => { 
            if(e.target.name() && (e.target.name().includes('drawing') || e.target.name().includes('borne'))) {
                App.saveState("Move/Drag"); 
                this.hasUnsavedChanges = true; 
            }
        });

        window.addEventListener('keydown', (e) => this.handleKeyInput(e));
        window.addEventListener('resize', () => {
            if(document.getElementById('workspace')) {
                App.stage.width(document.getElementById('workspace').offsetWidth);
                App.stage.height(document.getElementById('workspace').offsetHeight);
            }
        });
        window.addEventListener('beforeunload', (e) => { if (this.hasUnsavedChanges) { e.preventDefault(); e.returnValue = ''; } });
        
        this.transformer = new Konva.Transformer({ 
            borderStroke: '#3b82f6', anchorStroke: '#3b82f6', anchorFill: 'white', anchorSize: 8, 
            rotationSnaps: [0, 90, 180, 270] 
        });
        App.layers.ui.add(this.transformer);

        this.initOsnapMarker();
        this.createDynamicInput();
        console.log("Symbol Editor V8.5 (Trim Logic Fixed)");
    },

    // --- MOUSE EVENTS ---
    onMouseDown: function(e) {
        if (e.evt.button === 2) { // Botão Direito
            if (this.selectionRect) { this.selectionRect.destroy(); this.selectionRect = null; App.layers.ui.batchDraw(); return; }
            const toolName = App.state.tool;
            const tool = window.DrawingTools ? window.DrawingTools[toolName] : null;

            if (toolName === 'polyline' && tool && tool.clickCount > 0) { tool.finish(); App.setTool('cursor'); return; }
            if (toolName !== 'cursor' && tool && tool.clickCount > 0) { this.resetToolState(); App.setTool('cursor'); return; }
            if (toolName === 'cursor') { 
                if (App.state.lastDrawingTool) App.setTool(App.state.lastDrawingTool); 
                else this.clearSelection(); 
            } else { App.setTool('cursor'); }
            return;
        }
        
        if (e.evt.button !== 0) return;

        const pos = App.getPointerPos();
        const tool = App.state.tool;

        // TRIM (Aparar)
        if (tool === 'trim') {
            // Garante que clicou em um shape de desenho
            const shape = e.target;
            if(shape && shape.name() === 'drawing-shape') {
                this.performSmartTrim(shape, pos);
            }
            return;
        }

        // SELEÇÃO
        if (tool === 'cursor') {
            if (this.selectionRect) { this.finishSelectionBox(); return; }
            if (e.target === App.stage) { this.startSelectionBox(pos); } 
            else { if (!e.evt.ctrlKey) this.clearSelection(); this.selectObject(e.target); }
            return;
        }

        // DESENHO
        if (window.DrawingTools && window.DrawingTools[tool]) {
            const snapData = this.calculateOSNAP(pos);
            let finalPos = snapData.pos;
            if (!snapData.type && App.state.ortho) {
                const activeTool = window.DrawingTools[tool];
                const start = activeTool.startPos || activeTool.center;
                if (start) finalPos = App.applyOrthoConstraints(start, pos);
            }
            const result = window.DrawingTools[tool].handleClick(finalPos);
            this.hasUnsavedChanges = true;
            if (result && result.msg) {
                this.updateDynamicInput(result.msg, e.evt.clientX, e.evt.clientY);
                App.setPrompt(result.msg);
            } else {
                this.updateDynamicInput('');
                if(result && result.status === 'finished') App.setPrompt("Pronto.");
            }
        }
    },

    onMouseMove: function(e) {
        if (e.evt.buttons === 4) return;
        const pos = App.getPointerPos();
        const clientPos = { x: e.evt.clientX, y: e.evt.clientY };

        const footerCoords = document.getElementById('footer-coords');
        if(footerCoords) footerCoords.innerText = `X: ${pos.x.toFixed(2)}, Y: ${pos.y.toFixed(2)}`;

        if (this.cmdInput && !this.cmdInput.classList.contains('hidden')) {
            this.cmdInput.style.left = (clientPos.x + 20) + 'px';
            this.cmdInput.style.top = (clientPos.y + 20) + 'px';
        }

        if (this.selectionRect) { this.updateSelectionBox(pos); return; }

        const toolName = App.state.tool;
        
        // TRIM PREVIEW
        if(toolName === 'trim') { 
            this.performSmartTrimPreview(e.target); 
            return; 
        }

        const tool = window.DrawingTools ? window.DrawingTools[toolName] : null;
        
        if (tool && tool.ghost) {
            const snapData = this.calculateOSNAP(pos);
            let finalPos = snapData.pos;
            const basePos = tool.startPos || tool.center;

            if (!snapData.type && App.state.ortho && basePos) {
                 finalPos = App.applyOrthoConstraints(basePos, pos);
            }

            tool.handleMove(null, finalPos);
            if (basePos) { const dx = finalPos.x - basePos.x; const dy = finalPos.y - basePos.y; this.updateRelativeCoords(dx, dy); }
        } else {
            this.updateRelativeCoords(null, null);
            if(App.state.tool !== 'cursor') this.calculateOSNAP(pos);
        }
    },

    onMouseUp: function(e) { }, 

    // --- FUNÇÕES DE TRIM (CORRIGIDAS) ---
    performSmartTrimPreview: function(target) {
        const container = App.stage.container();
        container.style.cursor = 'crosshair';

        // 1. Limpa Highlight anterior (volta cor original)
        App.layers.main.find('.drawing-shape').forEach(node => {
            // Cor padrão cinza claro
            if (node.stroke() === '#ef4444') node.stroke('#cbd5e1');
        });

        // 2. Aplica Highlight no alvo atual
        if (target && target.name() === 'drawing-shape') {
            target.stroke('#ef4444'); // Vermelho
        }
        
        // Redesenha APENAS se necessário (otimização)
        App.layers.main.batchDraw();
    },

    performSmartTrim: function(target, clickPos) {
        if (!target || target.name() !== 'drawing-shape') return;
        
        // Fallback: Se não for Linha, deleta o objeto inteiro
        if (target.getClassName() !== 'Line' || target.points().length > 4) {
            target.destroy();
            App.layers.main.batchDraw();
            App.saveState("Quick Trim");
            return;
        }

        const pts = target.points();
        // Coordenadas absolutas
        const p1 = {x: pts[0] + target.x(), y: pts[1] + target.y()};
        const p2 = {x: pts[2] + target.x(), y: pts[3] + target.y()};

        let intersections = [];
        
        // Coleta shapes de todos os objetos do main layer
        let shapes = App.layers.main.getChildren();
        if(shapes.toArray) shapes = shapes.toArray();
        else if(!Array.isArray(shapes)) shapes = Array.from(shapes);

        shapes.forEach(other => {
            if (other === target || other.name() !== 'drawing-shape') return;
            
            // Interseção com Círculo
            if (other.getClassName() === 'Circle') {
                const ints = this.getLineCircleIntersection(p1, p2, {x: other.x(), y: other.y(), r: other.radius()});
                intersections = intersections.concat(ints);
            } 
            // Interseção com Outras Linhas/Formas
            else {
                const segments = this.getShapeSegments(other);
                segments.forEach(seg => {
                    const int = this.getLineIntersection(p1, p2, seg.p1, seg.p2);
                    if (int) intersections.push(int);
                });
            }
        });

        // Adiciona pontas da linha como fronteiras
        intersections.push(p1, p2);

        // Ordena pontos ao longo da linha (distância de P1)
        intersections.sort((a, b) => {
            const da = Math.pow(a.x - p1.x, 2) + Math.pow(a.y - p1.y, 2);
            const db = Math.pow(b.x - p1.x, 2) + Math.pow(b.y - p1.y, 2);
            return da - db;
        });

        // Descobre em qual segmento o clique ocorreu
        // Projeta o clique na reta para achar a posição relativa
        const proj = this.projectPointToLine(clickPos, p1, p2); 
        const clickOnLine = proj.point;
        
        // Distância do clique até a origem P1
        const distClick = Math.sqrt(Math.pow(clickOnLine.x - p1.x, 2) + Math.pow(clickOnLine.y - p1.y, 2));

        let segmentFound = false;

        // Itera intervalos entre interseções
        for(let i=0; i < intersections.length - 1; i++) {
            const start = intersections[i];
            const end = intersections[i+1];
            
            const dStart = Math.sqrt(Math.pow(start.x - p1.x, 2) + Math.pow(start.y - p1.y, 2));
            const dEnd = Math.sqrt(Math.pow(end.x - p1.x, 2) + Math.pow(end.y - p1.y, 2));

            // Verifica se o clique está neste intervalo (com tolerância de 2px)
            // dStart e dEnd já estão ordenados
            if (distClick >= dStart - 2 && distClick <= dEnd + 2) {
                // ESTE É O PEDACINHO A REMOVER!
                segmentFound = true;
                
                // 1. Destrói a linha original
                target.destroy();

                // 2. Recria os OUTROS segmentos (tudo menos o intervalo i)
                for(let j=0; j < intersections.length - 1; j++) {
                    if (j === i) continue; // Pula o segmento clicado (Trim)

                    const s = intersections[j];
                    const e = intersections[j+1];
                    // Ignora segmentos muito pequenos (erros de precisão)
                    if (Math.abs(s.x - e.x) < 0.5 && Math.abs(s.y - e.y) < 0.5) continue;

                    const newLine = new Konva.Line({
                        points: [s.x, s.y, e.x, e.y],
                        stroke: '#cbd5e1', strokeWidth: 2, 
                        name: 'drawing-shape', hitStrokeWidth: 10
                    });
                    App.layers.main.add(newLine);
                }
                
                App.layers.main.batchDraw();
                App.saveState("Smart Trim");
                return;
            }
        }
        
        // Se clicou na linha mas não achou segmento válido (ex: clique fora das interseções ou linha sem interseção)
        // Deleta ela inteira (comportamento Eraser)
        if (!segmentFound) {
            target.destroy();
            App.layers.main.batchDraw();
            App.saveState("Trim Delete");
        }
    },

    // --- GEOMETRIA ---
    getLineCircleIntersection: function(p1, p2, circle) {
        const dx = p2.x - p1.x; const dy = p2.y - p1.y;
        const cx = circle.x; const cy = circle.y; const r = circle.r;
        const A = dx*dx + dy*dy;
        const B = 2 * (dx * (p1.x - cx) + dy * (p1.y - cy));
        const C = (p1.x - cx) * (p1.x - cx) + (p1.y - cy) * (p1.y - cy) - r*r;
        const det = B*B - 4*A*C;
        const tVals = [];
        if (det < 0) return [];
        else if (det === 0) tVals.push(-B / (2*A));
        else { tVals.push((-B + Math.sqrt(det)) / (2*A)); tVals.push((-B - Math.sqrt(det)) / (2*A)); }
        const points = [];
        tVals.forEach(t => { if (t >= 0 && t <= 1) { points.push({ x: p1.x + t*dx, y: p1.y + t*dy }); } });
        return points;
    },
    
    // ... (Mantém helpers de OSNAP, Seleção e Teclado IGUAIS) ...
    // Estou colando os helpers essenciais para garantir que não falte nada
    getShapeSegments: function(shape) { const segments = []; const cn = shape.getClassName(); if (cn === 'Line') { const pts = shape.points(); const ox = shape.x(), oy = shape.y(); for(let i=0; i < pts.length - 2; i+=2) { segments.push({ p1: {x: pts[i]+ox, y: pts[i+1]+oy}, p2: {x: pts[i+2]+ox, y: pts[i+3]+oy} }); } } else if (cn === 'Rect') { const x=shape.x(), y=shape.y(), w=shape.width(), h=shape.height(); segments.push({p1:{x:x,y:y}, p2:{x:x+w,y:y}}); segments.push({p1:{x:x+w,y:y}, p2:{x:x+w,y:y+h}}); segments.push({p1:{x:x+w,y:y+h}, p2:{x:x,y:y+h}}); segments.push({p1:{x:x,y:y+h}, p2:{x:x,y:y}}); } else if (cn === 'RegularPolygon') { const sides = shape.sides(); const r = shape.radius(); const cx = shape.x(), cy = shape.y(); const rot = (shape.rotation() - 90) * Math.PI / 180; const points = []; for(let i=0; i<sides; i++) { const theta = (2 * Math.PI * i / sides) + rot; points.push({ x: cx + r * Math.cos(theta), y: cy + r * Math.sin(theta) }); } for(let i=0; i<sides; i++) { segments.push({ p1: points[i], p2: points[(i+1) % sides] }); } } return segments; },
    getLineIntersection: function(p1, p2, p3, p4) { const det = (p2.x - p1.x) * (p4.y - p3.y) - (p4.x - p3.x) * (p2.y - p1.y); if (det === 0) return null; const lambda = ((p4.y - p3.y) * (p4.x - p1.x) + (p3.x - p4.x) * (p4.y - p1.y)) / det; const gamma = ((p1.y - p2.y) * (p4.x - p1.x) + (p2.x - p1.x) * (p4.y - p1.y)) / det; if ((0 < lambda && lambda < 1) && (0 < gamma && gamma < 1)) { return { x: p1.x + lambda * (p2.x - p1.x), y: p1.y + lambda * (p2.y - p1.y) }; } return null; },
    getTangents: function(P, C, r) { const dx = C.x - P.x; const dy = C.y - P.y; const dist = Math.sqrt(dx*dx + dy*dy); if (dist <= r) return []; const alpha = Math.atan2(dy, dx); const beta = Math.asin(r / dist); const t1 = { x: C.x + r * Math.sin(alpha - beta - Math.PI/2), y: C.y - r * Math.cos(alpha - beta - Math.PI/2) }; const t2 = { x: C.x + r * Math.sin(alpha + beta - Math.PI/2), y: C.y - r * Math.cos(alpha + beta - Math.PI/2) }; return [t1, t2]; },
    projectPointToLine: function(p, a, b) { const atob = { x: b.x - a.x, y: b.y - a.y }; const atop = { x: p.x - a.x, y: p.y - a.y }; const len2 = atob.x * atob.x + atob.y * atob.y; let t = 0; if (len2 !== 0) t = (atop.x * atob.x + atop.y * atob.y) / len2; return { point: { x: a.x + t * atob.x, y: a.y + t * atob.y }, t: t }; },

    // ... (UI Helpers e OSNAP mantidos) ...
    createDynamicInput: function() { const div = document.createElement('div'); div.id = 'dynamic-input'; div.className = 'fixed hidden bg-slate-800 text-white px-3 py-2 rounded shadow-lg border border-slate-600 z-50 flex flex-col items-start font-mono text-sm'; div.style.pointerEvents = 'none'; div.innerHTML = `<div class="flex items-center gap-2"><span class="text-slate-400">CMD:</span><span id="cmd-text" class="font-bold text-yellow-400"></span></div><div id="rel-coords" class="text-[10px] text-slate-300 mt-1 hidden border-t border-slate-600 w-full pt-1"></div>`; document.body.appendChild(div); this.cmdInput = div; },
    updateDynamicInput: function(text, x, y, isTyping = false) { const elCmd = document.getElementById('cmd-text'); const tool = window.DrawingTools ? window.DrawingTools[App.state.tool] : null; const isDrawing = tool && tool.clickCount > 0; if (!text && !this.commandBuffer && !isDrawing) { this.cmdInput.classList.add('hidden'); return; } this.cmdInput.classList.remove('hidden'); elCmd.innerText = isTyping ? this.commandBuffer : text; if(x !== undefined) { this.cmdInput.style.left = (x + 20) + 'px'; this.cmdInput.style.top = (y + 20) + 'px'; } },
    updateRelativeCoords: function(dx, dy) { const elRel = document.getElementById('rel-coords'); if (dx !== null && !isNaN(dx)) { elRel.classList.remove('hidden'); const len = Math.sqrt(dx*dx + dy*dy).toFixed(1); const ang = Math.round(Math.atan2(dy, dx) * 180 / Math.PI); elRel.innerHTML = `<span class="text-blue-300">ΔX: ${dx.toFixed(1)}</span> <span class="text-red-300 ml-2">ΔY: ${dy.toFixed(1)}</span><br><span class="text-gray-400">Dist: ${len} < ${ang}°</span>`; } else { elRel.classList.add('hidden'); } },
    initOsnapMarker: function() { this.osnapGroup = new Konva.Group({ visible: false, listening: false }); this.osnapGroup.add(new Konva.Rect({ name: 'endpoint', x: -5, y: -5, width: 10, height: 10, stroke: '#4ade80', strokeWidth: 2, visible: false })); this.osnapGroup.add(new Konva.Line({ name: 'midpoint', points: [0, -6, 5, 4, -5, 4], closed: true, stroke: '#4ade80', strokeWidth: 2, visible: false })); this.osnapGroup.add(new Konva.Line({ name: 'intersection', points: [-4, -4, 4, 4, 0, 0, 4, -4, -4, 4], stroke: '#4ade80', strokeWidth: 2, visible: false })); this.osnapGroup.add(new Konva.Circle({ name: 'center', radius: 5, stroke: '#4ade80', strokeWidth: 2, visible: false })); this.osnapGroup.add(new Konva.Line({ name: 'nearest', points: [-4, -4, 4, -4, -4, 4, 4, 4], closed: true, stroke: '#4ade80', strokeWidth: 1, visible: false })); this.osnapGroup.add(new Konva.Line({ name: 'perpendicular', points: [-4, 4, -4, -4, 4, -4], stroke: '#4ade80', strokeWidth: 2, visible: false })); this.osnapGroup.add(new Konva.Line({ name: 'quadrant', points: [0, -6, 4, 0, 0, 6, -4, 0], closed: true, stroke: '#4ade80', strokeWidth: 2, visible: false })); this.osnapGroup.add(new Konva.Group({ name: 'tangent', visible: false }).add(new Konva.Circle({ radius: 5, stroke: '#4ade80', strokeWidth: 2 })).add(new Konva.Line({ points: [-5, 5, 5, 5], stroke: '#4ade80', strokeWidth: 2 }))); App.layers.ui.add(this.osnapGroup); },
    showOsnapMarker: function(pos, type) { if(!pos) { this.osnapGroup.visible(false); return; } this.osnapGroup.position(pos); this.osnapGroup.children.forEach(c => c.visible(false)); const shape = this.osnapGroup.findOne('.' + type); if(shape) { shape.visible(true); if(shape.children) shape.children.forEach(c => c.visible(true)); } else { this.osnapGroup.findOne('.endpoint').visible(true); } this.osnapGroup.visible(true); App.layers.ui.batchDraw(); },
    selectObject: function(node) { if(this.selectedNodes.includes(node)) return; if(node.name() !== 'drawing-shape' && node.name() !== 'borne-group') return; this.selectedNodes.push(node); this.originalStyles.set(node, { stroke: node.stroke(), dash: node.dash() }); node.stroke('#3b82f6'); node.dash([5, 5]); this.transformer.nodes(this.selectedNodes); node.draggable(true); App.layers.main.batchDraw(); App.layers.ui.batchDraw(); },
    clearSelection: function() { if (App.state.tool === 'move') return; this.selectedNodes.forEach(node => { const style = this.originalStyles.get(node); if(style) { node.stroke(style.stroke); node.dash(style.dash || []); } node.draggable(false); }); this.originalStyles.clear(); this.selectedNodes = []; this.transformer.nodes([]); App.layers.main.batchDraw(); App.layers.ui.batchDraw(); },
    startSelectionBox: function(pos) { this.clearSelection(); this.selectionStart = pos; this.selectionRect = new Konva.Rect({ x: pos.x, y: pos.y, width: 0, height: 0, fill: 'rgba(0,0,255,0.1)', stroke: 'blue', strokeWidth: 1, listening: false }); App.layers.ui.add(this.selectionRect); },
    updateSelectionBox: function(pos) { const w = pos.x - this.selectionStart.x; const h = pos.y - this.selectionStart.y; this.selectionRect.width(w); this.selectionRect.height(h); if (w < 0) { this.selectionRect.fill('rgba(74, 222, 128, 0.2)'); this.selectionRect.stroke('#22c55e'); this.selectionRect.dash([4, 4]); } else { this.selectionRect.fill('rgba(59, 130, 246, 0.2)'); this.selectionRect.stroke('#3b82f6'); this.selectionRect.dash([]); } App.layers.ui.batchDraw(); },
    finishSelectionBox: function() { if (!this.selectionRect) return; const box = this.selectionRect.getClientRect(); const isCrossing = this.selectionRect.width() < 0; this.selectionRect.destroy(); this.selectionRect = null; let shapes = App.layers.main.getChildren(); if(shapes.toArray) shapes = shapes.toArray(); else if(!Array.isArray(shapes)) shapes = []; shapes.forEach(shape => { if(shape.name() !== 'drawing-shape') return; if (isCrossing) { if (Konva.Util.haveIntersection(box, shape.getClientRect())) this.selectObject(shape); } else { const s = shape.getClientRect(); if (s.x >= box.x && s.y >= box.y && (s.x + s.width) <= (box.x + box.width) && (s.y + s.height) <= (box.y + box.height)) this.selectObject(shape); } }); App.layers.ui.batchDraw(); },
    handleKeyInput: function(e) { if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return; if (e.ctrlKey || e.metaKey || e.altKey) return; if (e.key === 'Delete' || e.key === 'Backspace') { e.preventDefault(); if (this.commandBuffer.length > 0) { this.commandBuffer = this.commandBuffer.slice(0, -1); const ptr = App.stage.getPointerPosition() || {x:0, y:0}; this.updateDynamicInput(null, ptr.x, ptr.y, true); return; } if (this.selectedNodes.length > 0) { this.selectedNodes.forEach(n => n.destroy()); this.clearSelection(); App.saveState("Delete"); this.hasUnsavedChanges = true; return; } } if (e.key === 'Escape') { const tool = window.DrawingTools ? window.DrawingTools[App.state.tool] : null; if(tool && tool.finish) tool.finish(); this.commandBuffer = ''; this.updateDynamicInput(''); this.resetToolState(); this.clearSelection(); App.setTool('cursor'); return; } if (e.key === 'Enter') { if(App.state.tool === 'polyline') window.DrawingTools.polyline.finish(); else if (App.state.tool === 'move' && window.DrawingTools.move.step === -1) { const res = window.DrawingTools.move.confirmSelection(); App.setPrompt(res.msg); } else this.processCommand(this.commandBuffer); this.commandBuffer = ''; this.updateDynamicInput(''); return; } if (e.key.length === 1 && /[a-zA-Z0-9@,\.\-<]/.test(e.key)) { if(this.commandBuffer === '' && !['@','-','.','<'].includes(e.key)) { const k = e.key.toLowerCase(); if(k === 'l') { App.setTool('line'); return; } if(k === 'p') { App.setTool('polyline'); return; } if(k === 'r') { App.setTool('rect'); return; } if(k === 'c') { App.setTool('circle'); return; } if(k === 'm') { App.setTool('move'); return; } if(k === 't') { if(!this.commandBuffer.startsWith('t')) { App.setTool('text'); return; } } if(k === 'g') { App.togglePolygonMenu(); return; } } this.commandBuffer += e.key; const ptr = App.stage.getPointerPosition() || {x:0, y:0}; this.updateDynamicInput(null, ptr.x, ptr.y, true); } },
    processCommand: function(cmd) { const toolName = App.state.tool; const tool = window.DrawingTools ? window.DrawingTools[toolName] : null; if (!tool) return; if (/^\d+\.?\d*$/.test(cmd)) { const val = parseFloat(cmd); if ((toolName === 'line' || toolName === 'polyline') && tool.startPos) { const mousePos = App.getPointerPos(); const dx = mousePos.x - tool.startPos.x; const dy = mousePos.y - tool.startPos.y; const currentLen = Math.sqrt(dx*dx + dy*dy); const ratio = val / (currentLen || 1); const finalPos = { x: tool.startPos.x + dx * ratio, y: tool.startPos.y + dy * ratio }; const result = tool.handleClick(finalPos); if(result && result.msg) { this.updateDynamicInput(result.msg, 0, 0); App.setPrompt(result.msg); } return; } const result = tool.handleClick(val); if(result && result.msg) App.setPrompt(result.msg); else App.setPrompt("Pronto."); return; } if (window.CadUtils) { const lastPoint = tool.startPos || tool.center || null; let mousePos = App.getPointerPos(); if (App.state.ortho && lastPoint) { mousePos = App.applyOrthoConstraints(lastPoint, mousePos); } const finalPos = CadUtils.parseInput(cmd, lastPoint, mousePos); if (finalPos) { const result = tool.handleClick(finalPos); if(result && result.msg) { this.updateDynamicInput(result.msg, 0, 0); App.setPrompt(result.msg); } } else { console.warn("Comando inválido:", cmd); this.updateDynamicInput("Inválido", 0, 0); } } },
    resetToolState: function() { const tool = window.DrawingTools ? window.DrawingTools[App.state.tool] : null; if(tool && tool.reset) { tool.reset(); } this.updateDynamicInput('', 0, 0); this.updateRelativeCoords(null, null); this.showOsnapMarker(null); App.setPrompt("Pronto."); },
    calculateOSNAP: function(mousePos) { if (!App.state.osnap) { this.showOsnapMarker(null); return { pos: mousePos }; } const threshold = 15 / App.state.scale; const modes = App.state.osnapModes; let candidatesHigh = []; let candidatesLow = []; const addCandidate = (point, type, isHighPriority = true) => { const d = Math.sqrt(Math.pow(point.x - mousePos.x, 2) + Math.pow(point.y - mousePos.y, 2)); if (d < threshold) { const list = isHighPriority ? candidatesHigh : candidatesLow; list.push({ pos: point, dist: d, type: type, priority: isHighPriority ? 1 : 3 }); } }; let shapes = App.layers.main.getChildren(); if (shapes.toArray) shapes = shapes.toArray(); else if (Array.isArray(shapes)) shapes = shapes.slice(); const activeTool = window.DrawingTools ? window.DrawingTools[App.state.tool] : null; if (App.state.tool === 'polyline' && activeTool && activeTool.ghost) { shapes.push(activeTool.ghost); } const allLines = []; shapes.forEach(shape => { if(!shape) return; const isMain = shape.getLayer() === App.layers.main; const isGhost = shape.getLayer() === App.layers.temp; if(!isMain && !isGhost) return; if(isMain && shape.name() !== 'drawing-shape') return; const segments = this.getShapeSegments(shape); if (isGhost && App.state.tool === 'polyline' && segments.length > 0) { segments.pop(); } segments.forEach(seg => { const p1 = seg.p1; const p2 = seg.p2; allLines.push({p1, p2}); if(modes.endpoint) { addCandidate(p1, 'endpoint', true); addCandidate(p2, 'endpoint', true); } if(modes.midpoint) { addCandidate({x: (p1.x+p2.x)/2, y: (p1.y+p2.y)/2}, 'midpoint', true); } const activeTool = window.DrawingTools[App.state.tool]; if(modes.perpendicular && activeTool && activeTool.startPos) { const P = activeTool.startPos; const proj = this.projectPointToLine(P, p1, p2); if(proj.t >= 0 && proj.t <= 1) addCandidate(proj.point, 'perpendicular', false); } if(modes.nearest) { const proj = this.projectPointToLine(mousePos, p1, p2); if(proj.t >= 0 && proj.t <= 1) addCandidate(proj.point, 'nearest', false); } }); if(shape.getClassName() === 'Circle' || shape.getClassName() === 'RegularPolygon') { const center = {x: shape.x(), y: shape.y()}; const r = shape.radius(); if(modes.center) addCandidate(center, 'center', true); if(shape.getClassName() === 'Circle') { if(modes.quadrant) { addCandidate({x: center.x + r, y: center.y}, 'quadrant', true); addCandidate({x: center.x - r, y: center.y}, 'quadrant', true); addCandidate({x: center.x, y: center.y + r}, 'quadrant', true); addCandidate({x: center.x, y: center.y - r}, 'quadrant', true); } if(modes.tangent && activeTool && activeTool.startPos) { const tangents = this.getTangents(activeTool.startPos, center, r); tangents.forEach(t => addCandidate(t, 'tangent', false)); } } } }); if(modes.intersection) { for(let i=0; i<allLines.length; i++) { for(let j=i+1; j<allLines.length; j++) { const int = this.getLineIntersection(allLines[i].p1, allLines[i].p2, allLines[j].p1, allLines[j].p2); if(int) addCandidate(int, 'intersection', true); } } } if (candidatesHigh.length > 0) { candidatesHigh.sort((a, b) => a.dist - b.dist); const winner = candidatesHigh[0]; this.showOsnapMarker(winner.pos, winner.type); return { pos: winner.pos, type: winner.type }; } else if (candidatesLow.length > 0) { candidatesLow.sort((a, b) => a.dist - b.dist); const winner = candidatesLow[0]; this.showOsnapMarker(winner.pos, winner.type); return { pos: winner.pos, type: winner.type }; } else { this.showOsnapMarker(null); return { pos: mousePos }; } },
};

window.onload = function() { window.Editor.init(); };