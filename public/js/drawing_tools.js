window.DrawingTools = {
    // --- MOVER (Lógica Simplificada: Selecionar Antes) ---
    move: {
        clickCount: 0, 
        startPos: null,
        ghosts: [],

        init: function() {
            // Validação Imediata
            if (window.Editor.selectedNodes.length === 0) {
                // Se não tem nada selecionado, aborta e volta pro cursor
                setTimeout(() => App.setTool('cursor'), 50); // Timeout para não conflitar com o ciclo atual
                return { msg: 'Nada selecionado. Selecione antes de mover.' };
            }
            
            // Prepara para mover
            this.clickCount = 0;
            return { msg: 'Especifique o ponto base' };
        },

        handleClick: function(pos) {
            // Proteção extra
            if (window.Editor.selectedNodes.length === 0) {
                App.setTool('cursor');
                return { status: 'finished' };
            }

            if (this.clickCount === 0) {
                // CLIQUE 1: Ponto Base
                this.clickCount = 1;
                this.startPos = pos;

                // Cria Ghosts
                this.ghosts = [];
                window.Editor.selectedNodes.forEach(node => {
                    const ghost = node.clone();
                    ghost.opacity(0.5); 
                    ghost.stroke('#3b82f6'); 
                    ghost.listening(false);
                    // Remove ID para não conflitar
                    ghost.id(ghost.id() + '_ghost'); 
                    App.layers.temp.add(ghost);
                    this.ghosts.push(ghost);
                });
                
                // Esconde Transformer
                window.Editor.transformer.visible(false);
                App.layers.temp.batchDraw();
                App.layers.ui.batchDraw();

                return { status: 'drawing', msg: 'Ponto de destino' };
            } else {
                // CLIQUE 2: Destino
                const dx = pos.x - this.startPos.x;
                const dy = pos.y - this.startPos.y;

                // Move objetos originais
                window.Editor.selectedNodes.forEach(node => {
                    node.x(node.x() + dx);
                    node.y(node.y() + dy);
                });

                // Finaliza e Restaura
                window.Editor.transformer.visible(true);
                window.Editor.transformer.forceUpdate(); 
                
                this.reset();
                App.layers.main.batchDraw();
                App.layers.ui.batchDraw();
                App.saveState("Move Objects");
                
                App.setTool('cursor');
                return { status: 'finished', msg: 'Pronto.' };
            }
        },

        handleMove: function(startPos, currentPos) {
            if (this.ghosts.length > 0) {
                const dx = currentPos.x - this.startPos.x;
                const dy = currentPos.y - this.startPos.y;
                
                this.ghosts.forEach((ghost, i) => {
                    // Truque: O ghost[i] corresponde ao selectedNodes[i] original
                    // Como não movemos o original ainda, pegamos a posição dele
                    const original = window.Editor.selectedNodes[i];
                    ghost.x(original.x() + dx);
                    ghost.y(original.y() + dy);
                });
                App.layers.temp.batchDraw();
            }
        },

        reset: function() {
            this.clickCount = 0;
            this.startPos = null;
            this.ghosts.forEach(g => g.destroy());
            this.ghosts = [];
            App.layers.temp.batchDraw();
        }
    },
    
    // --- TRIM (Mantido) ---
    trim: {
        clickCount: 0,
        init: function() { return { msg: 'Clique na linha para aparar' }; },
        handleClick: function() { return { status: 'drawing', msg: 'Clique na linha para aparar' }; },
        handleMove: function() {},
        reset: function() {}
    },

    // --- FERRAMENTAS DE DESENHO (Mantidas) ---
    line: {
        clickCount: 0, ghost: null, startPos: null,
        init: function() { return { msg: 'Primeiro ponto' }; },
        handleClick: function(pos) {
            if (this.clickCount === 0) {
                this.clickCount = 1; this.startPos = pos;
                this.ghost = new Konva.Line({ points: [pos.x, pos.y, pos.x, pos.y], stroke: '#22d3ee', strokeWidth: 2, dash: [5, 5] });
                App.layers.temp.add(this.ghost); App.layers.temp.batchDraw();
                return { status: 'drawing', msg: 'Próximo ponto' };
            } else {
                const points = this.ghost.points();
                if(pos) { points[2] = pos.x; points[3] = pos.y; }
                const final = new Konva.Line({ points: points, stroke: '#cbd5e1', strokeWidth: 2, name: 'drawing-shape', hitStrokeWidth: 10 });
                App.layers.main.add(final); this.reset(); App.saveState("Draw Line"); return { status: 'finished' };
            }
        },
        handleMove: function(startPos, currentPos) { if (this.ghost) { const pts = this.ghost.points(); pts[2] = currentPos.x; pts[3] = currentPos.y; this.ghost.points(pts); App.layers.temp.batchDraw(); } },
        reset: function() { this.clickCount = 0; this.startPos = null; if(this.ghost) this.ghost.destroy(); this.ghost = null; App.layers.temp.batchDraw(); App.layers.main.batchDraw(); }
    },
    rect: {
        clickCount: 0, ghost: null, startPos: null,
        init: function() { return { msg: 'Primeiro canto' }; },
        handleClick: function(pos) {
            if (this.clickCount === 0) {
                this.clickCount = 1; this.startPos = pos;
                this.ghost = new Konva.Rect({ x: pos.x, y: pos.y, width: 0, height: 0, stroke: '#22d3ee', strokeWidth: 2, dash: [5, 5] });
                App.layers.temp.add(this.ghost); App.layers.temp.batchDraw();
                return { status: 'drawing', msg: 'Canto oposto' };
            } else {
                if(pos) { this.ghost.width(pos.x - this.startPos.x); this.ghost.height(pos.y - this.startPos.y); }
                const final = new Konva.Rect({ x: this.startPos.x, y: this.startPos.y, width: this.ghost.width(), height: this.ghost.height(), stroke: '#cbd5e1', strokeWidth: 2, name: 'drawing-shape' });
                App.layers.main.add(final); this.reset(); App.saveState("Draw Rect"); return { status: 'finished' };
            }
        },
        handleMove: function(startPos, currentPos) { if (this.ghost) { this.ghost.width(currentPos.x - this.startPos.x); this.ghost.height(currentPos.y - this.startPos.y); App.layers.temp.batchDraw(); } },
        reset: function() { this.clickCount = 0; this.startPos = null; if(this.ghost) this.ghost.destroy(); this.ghost = null; App.layers.temp.batchDraw(); App.layers.main.batchDraw(); }
    },
    circle: {
        clickCount: 0, ghost: null, center: null,
        init: function() { return { msg: 'Centro' }; },
        handleClick: function(pos) {
            if (typeof pos === 'number') { if (this.clickCount === 1) { this.ghost.radius(pos); this.finish(); return { status: 'finished' }; } return; }
            if (this.clickCount === 0) {
                this.clickCount = 1; this.center = pos;
                this.ghost = new Konva.Circle({ x: pos.x, y: pos.y, radius: 0, stroke: '#22d3ee', strokeWidth: 2, dash: [5, 5] });
                App.layers.temp.add(this.ghost); App.layers.temp.batchDraw();
                return { status: 'drawing', msg: 'Raio' };
            } else {
                if(pos) { const dx = pos.x - this.center.x; const dy = pos.y - this.center.y; this.ghost.radius(Math.sqrt(dx*dx + dy*dy)); }
                this.finish(); return { status: 'finished' };
            }
        },
        handleMove: function(startPos, currentPos) { if (this.ghost) { const dx = currentPos.x - this.center.x; const dy = currentPos.y - this.center.y; this.ghost.radius(Math.sqrt(dx*dx + dy*dy)); App.layers.temp.batchDraw(); } },
        finish: function() { const final = new Konva.Circle({ x: this.center.x, y: this.center.y, radius: this.ghost.radius(), stroke: '#cbd5e1', strokeWidth: 2, name: 'drawing-shape' }); App.layers.main.add(final); this.reset(); App.saveState("Draw Circle"); },
        reset: function() { this.clickCount = 0; this.center = null; if(this.ghost) this.ghost.destroy(); this.ghost = null; App.layers.temp.batchDraw(); App.layers.main.batchDraw(); }
    },
    polygon: {
        clickCount: 0, ghost: null, startPos: null, sides: 6,
        init: function() { return { msg: 'Centro' }; },
        handleClick: function(pos) {
            if (typeof pos === 'number') { if (this.clickCount === 1) { this.ghost.radius(pos); this.finish(); return { status: 'finished' }; } return; }
            if (this.clickCount === 0) {
                this.clickCount = 1; this.startPos = pos;
                this.ghost = new Konva.RegularPolygon({ x: pos.x, y: pos.y, sides: this.sides, radius: 0, stroke: '#22d3ee', strokeWidth: 2, dash: [5, 5] });
                App.layers.temp.add(this.ghost); App.layers.temp.batchDraw();
                return { status: 'drawing', msg: 'Raio' };
            } else {
                if(pos) { const dx = pos.x - this.startPos.x; const dy = pos.y - this.startPos.y; this.ghost.radius(Math.sqrt(dx*dx + dy*dy)); const angle = Math.atan2(dy, dx) * 180 / Math.PI; this.ghost.rotation(angle + 90); }
                this.finish(); return { status: 'finished' };
            }
        },
        handleMove: function(startPos, currentPos) { if (this.ghost) { const dx = currentPos.x - this.startPos.x; const dy = currentPos.y - this.startPos.y; this.ghost.radius(Math.sqrt(dx*dx + dy*dy)); const angle = Math.atan2(dy, dx) * 180 / Math.PI; this.ghost.rotation(angle + 90); App.layers.temp.batchDraw(); } },
        finish: function() { const final = new Konva.RegularPolygon({ x: this.startPos.x, y: this.startPos.y, sides: this.sides, radius: this.ghost.radius(), rotation: this.ghost.rotation(), stroke: '#cbd5e1', strokeWidth: 2, name: 'drawing-shape' }); App.layers.main.add(final); this.reset(); App.saveState("Draw Polygon"); },
        reset: function() { this.clickCount = 0; this.startPos = null; if(this.ghost) this.ghost.destroy(); this.ghost = null; App.layers.temp.batchDraw(); App.layers.main.batchDraw(); }
    },
    polyline: {
        clickCount: 0, ghost: null, startPos: null, points: [],
        init: function() { return { msg: 'Ponto inicial' }; },
        handleClick: function(pos) {
            if (this.clickCount === 0) {
                this.clickCount = 1; this.points = [pos.x, pos.y, pos.x, pos.y]; this.startPos = pos;
                this.ghost = new Konva.Line({ points: this.points, stroke: '#22d3ee', strokeWidth: 2, dash: [5, 5] });
                App.layers.temp.add(this.ghost); App.layers.temp.batchDraw();
                return { status: 'drawing', msg: 'Próximo (Enter finaliza)' };
            } else {
                const currentPts = this.ghost.points();
                currentPts[currentPts.length-2] = pos.x; currentPts[currentPts.length-1] = pos.y;
                currentPts.push(pos.x, pos.y);
                this.ghost.points(currentPts);
                this.startPos = pos;
                return { status: 'drawing', msg: 'Próximo...' };
            }
        },
        handleMove: function(startPos, currentPos) { if (this.ghost) { const pts = this.ghost.points(); pts[pts.length-2] = currentPos.x; pts[pts.length-1] = currentPos.y; this.ghost.points(pts); App.layers.temp.batchDraw(); } },
        finish: function() {
            if(!this.ghost) return;
            const pts = this.ghost.points(); pts.pop(); pts.pop();
            if(pts.length >= 4) { const final = new Konva.Line({ points: pts, stroke: '#cbd5e1', strokeWidth: 2, name: 'drawing-shape', hitStrokeWidth: 10 }); App.layers.main.add(final); App.saveState("Draw Polyline"); }
            this.reset();
        },
        reset: function() { this.clickCount = 0; this.startPos = null; this.points = []; if(this.ghost) this.ghost.destroy(); this.ghost = null; App.layers.temp.batchDraw(); App.layers.main.batchDraw(); }
    },
    text: {
        init: function() { return { msg: 'Ponto de inserção' }; },
        handleClick: function(pos) {
            const txt = prompt("Texto:", "Texto");
            if(txt) {
                const text = new Konva.Text({ x: pos.x, y: pos.y, text: txt, fontSize: 14, fill: '#cbd5e1', draggable: true, name: 'drawing-shape' });
                App.layers.main.add(text); App.saveState("Add Text");
            }
            App.setTool('cursor'); return { status: 'finished' };
        },
        handleMove: function() {}, reset: function() {}
    }
};