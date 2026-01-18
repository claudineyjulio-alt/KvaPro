<?= $this->extend('layouts/editor_layout') ?>

<?= $this->section('styles') ?>
<style>
    /* Estilos Específicos do Canvas Grid */
    .cad-grid {
        background-color: #0f172a; /* Slate 900 */
        background-size: 40px 40px;
        background-image:
            linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <!-- Canvas Container (Pan/Zoom Wrapper) -->
    <div id="container" class="absolute inset-0 cad-grid"></div>
    
    <!-- Barra de Status Inferior -->
    <div class="absolute bottom-0 left-0 right-0 h-6 bg-slate-900 border-t border-slate-800 flex items-center px-4 text-[10px] text-slate-500 justify-between select-none pointer-events-none z-20">
        <div class="flex gap-4 font-mono">
            <span id="coords">X: 0.0 Y: 0.0</span>
            <span>GRID: <span class="text-green-500">ON</span></span>
            <span>SNAP: <span class="text-green-500">ON</span></span>
        </div>
        <div>Layer: <span class="text-white">01_DIAGRAMA</span></div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // --- 1. SETUP INICIAL ---
    const width = document.getElementById('workspace').offsetWidth;
    const height = document.getElementById('workspace').offsetHeight;
    const contadores = { componentes: {}, fios: 0 }; 
    
    let currentTool = 'cursor'; 
    let simboloParaInserir = null;
    let selectedObject = null; 
    let isDrawingWire = false;
    let wireStartNode = null; 
    let tempWireLine = null;  

    // Inicializa Konva
    const stage = new Konva.Stage({
        container: 'container',
        width: width,
        height: height,
        draggable: true // Pan com botão do meio/direito
    });

    const layerGrid = new Konva.Layer();
    const layerWires = new Konva.Layer(); 
    const layerComponents = new Konva.Layer(); 
    const layerUI = new Konva.Layer(); 
    stage.add(layerGrid, layerWires, layerComponents, layerUI);

    // --- 2. FERRAMENTAS E UI ---
    
    // Função global chamada pelos botões da Toolbar
    window.setTool = function(toolName) {
        currentTool = toolName;
        simboloParaInserir = null;
        
        // Atualiza UI da Toolbar
        const btnCursor = document.getElementById('btn-cursor');
        const btnCabo = document.getElementById('btn-cabo');
        const btnPan = document.getElementById('btn-pan');
        
        // Reset classes
        [btnCursor, btnCabo, btnPan].forEach(btn => {
            if(btn) {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('text-slate-300', 'hover:bg-slate-700');
            }
        });

        // Highlight active
        let activeBtn = null;
        if(toolName === 'cursor') activeBtn = btnCursor;
        if(toolName === 'cabo') activeBtn = btnCabo;
        if(toolName === 'pan') activeBtn = btnPan;

        if(activeBtn) {
            activeBtn.classList.remove('text-slate-300', 'hover:bg-slate-700');
            activeBtn.classList.add('bg-blue-600', 'text-white');
        }

        // Cursor do Mouse
        const ws = document.getElementById('workspace');
        if(toolName === 'cabo') ws.classList.add('cursor-crosshair');
        else if(toolName === 'pan') ws.classList.add('cursor-grab');
        else ws.classList.remove('cursor-crosshair', 'cursor-grab');

        if(toolName === 'pan') stage.draggable(true);
        else stage.draggable(false); // Só arrasta tela no modo Pan

        selecionarObjeto(null);
    }

    // Chamada pela Sidebar de Biblioteca
    window.selecionarPeloID = function(id) {
        // window.biblioteca é injetado no final da sidebar
        if(window.biblioteca && window.biblioteca[id]) {
            setTool('inserir');
            simboloParaInserir = window.biblioteca[id];
            // Feedback visual?
        }
    }

    // --- 3. EVENTOS DE CLIQUE E MOUSE ---
    
    stage.on('click tap', function (e) {
        // Clique no Vazio
        if (e.target === stage) {
            selecionarObjeto(null);
            
            if (currentTool === 'inserir' && simboloParaInserir) {
                // Pega posição relativa ao zoom/pan
                const pos = stage.getRelativePointerPosition();
                // Snap to Grid 10px
                const snapX = Math.round(pos.x / 10) * 10;
                const snapY = Math.round(pos.y / 10) * 10;
                
                criarComponente(simboloParaInserir, snapX, snapY);
                
                // Volta para cursor normal? Ou mantém carimbo?
                // setTool('cursor'); 
            }
            return;
        }

        if (e.target.hasName('fio-handle')) return; // Deixa o drag agir

        // Seleção
        let group = e.target.findAncestor('.componente');
        if (group && currentTool === 'cursor') {
            selecionarObjeto(group);
            return;
        }
        if (e.target.hasName('fio') && currentTool === 'cursor') {
            selecionarObjeto(e.target.parent); 
        }
    });

    // Coordenadas no Rodapé
    stage.on('mousemove', function() {
        const pos = stage.getRelativePointerPosition();
        if(pos) {
            document.getElementById('coords').innerText = `X: ${pos.x.toFixed(1)} Y: ${pos.y.toFixed(1)}`;
        }
        
        // Preview do Cabo
        if (isDrawingWire && tempWireLine) {
            const tr = layerWires.getAbsoluteTransform().copy().invert();
            const start = tr.point(wireStartNode.getAbsolutePosition());
            const pts = calcularRotaOrtogonal(start.x, start.y, pos.x, pos.y);
            tempWireLine.points(pts);
            layerWires.batchDraw();
        }
    });

    // --- 4. COMPONENTES ---
    
    function criarComponente(dados, x, y) {
        const prefixo = dados.sigla_padrao || 'X';
        let tagFinal = dados.categoria === 'bobina' ? 
            `-${prefixo}${++(contadores.componentes[prefixo]||(contadores.componentes[prefixo]=0))}` : 
            `-${prefixo}?`;

        const group = new Konva.Group({
            x: x, y: y, draggable: true,
            name: 'componente',
            id: 'comp_' + Date.now(),
            metaData: { tag: tagFinal, fabricante: '', categoria: dados.categoria }
        });

        // Renderiza SVG
        let svgCode = dados.simbolo_svg || '<rect width="50" height="50" stroke="red"/>';
        svgCode = svgCode.replace(/<\/?svg[^>]*>/g, ""); 
        
        // Cor do traço adaptada para fundo escuro (Slate 950)
        const svgString = `<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">
            <g stroke="#cbd5e1" stroke-width="2" fill="none">${svgCode}</g>
        </svg>`;
        
        const blob = new Blob([svgString], {type: 'image/svg+xml'});
        const url = URL.createObjectURL(blob);
        const image = new Image();
        
        image.onload = () => {
            const kImage = new Konva.Image({
                image: image, width: 100, height: 100, offsetX: 50, offsetY: 50
            });
            group.add(kImage);
            kImage.moveToBottom();
            
            // Caixa de Seleção
            const selectionBox = new Konva.Rect({
                x: -50, y: -50, width: 100, height: 100,
                stroke: '#3b82f6', strokeWidth: 1, dash: [5, 5],
                visible: false, name: 'selectionBox'
            });
            group.add(selectionBox);
        };
        image.src = url;

        // Texto da Tag
        const text = new Konva.Text({
            text: tagFinal, x: -160, y: -10, width: 100, align: 'right',
            fontSize: 12, fontFamily: 'monospace', fill: '#94a3b8', fontStyle: 'bold',
            name: 'tagText'
        });
        group.add(text);

        // Bornes
        let bornes = dados.bornes;
        if (typeof bornes === 'string') { try { bornes = JSON.parse(bornes); } catch(e) { bornes = []; } }

        if (bornes && Array.isArray(bornes)) {
            bornes.forEach(b => {
                const bx = parseFloat(b.x) - 50;
                const by = parseFloat(b.y) - 50;
                // Borne invisível (área de clique)
                const borneCircle = new Konva.Circle({
                    x: bx, y: by, radius: 4,
                    fill: 'transparent', name: 'borne', id: b.id
                });

                borneCircle.on('mouseenter', function() { 
                    if(currentTool === 'cabo') { this.fill('#4ade80'); this.radius(5); } // Verde
                });
                borneCircle.on('mouseleave', function() { 
                    if(!isDrawingWire) { this.fill('transparent'); this.radius(4); } 
                });
                borneCircle.on('click', function(e) {
                    if (currentTool === 'cabo') {
                        e.cancelBubble = true;
                        iniciarOuTerminarCabo(this);
                    }
                });
                group.add(borneCircle);
            });
        }

        // Drag e Seleção
        group.on('click', () => { if(currentTool === 'cursor') selecionarObjeto(group); });
        
        group.on('dragmove', function() {
            this.x(Math.round(this.x() / 10) * 10);
            this.y(Math.round(this.y() / 10) * 10);
            atualizarCabosConectados(this);
        });

        layerComponents.add(group);
    }

    // --- 5. LÓGICA DE CABOS (MANHATTAN + ALÇAS) ---
    // (Mesma lógica robusta que desenvolvemos)

    function calcularRotaOrtogonal(x1, y1, x2, y2, mode = 'HVH', offset = null) {
        let meio;
        if (mode === 'HVH') {
            meio = (offset !== null) ? offset : (x1 + x2) / 2;
            return [x1, y1, meio, y1, meio, y2, x2, y2];
        } else {
            meio = (offset !== null) ? offset : (y1 + y2) / 2;
            return [x1, y1, x1, meio, x2, meio, x2, y2];
        }
    }

    function iniciarOuTerminarCabo(borneNode) {
        const absPos = borneNode.getAbsolutePosition();
        const tr = layerWires.getAbsoluteTransform().copy().invert();
        const localPos = tr.point(absPos);

        if (!isDrawingWire) {
            isDrawingWire = true; wireStartNode = borneNode;
            tempWireLine = new Konva.Line({ 
                points: [localPos.x, localPos.y, localPos.x, localPos.y], 
                stroke: '#64748b', strokeWidth: 2, dash: [4, 4] 
            });
            layerWires.add(tempWireLine);
        } else {
            isDrawingWire = false;
            if(tempWireLine) tempWireLine.destroy();
            wireStartNode.fill('transparent');

            const startPos = tr.point(wireStartNode.getAbsolutePosition());
            const dx = Math.abs(startPos.x - localPos.x);
            const dy = Math.abs(startPos.y - localPos.y);
            const mode = dx > dy ? 'HVH' : 'VHV';

            const pts = calcularRotaOrtogonal(startPos.x, startPos.y, localPos.x, localPos.y, mode);
            
            contadores.fios++;
            const tagCabo = 'W' + contadores.fios;

            const grupo = new Konva.Group({ name: 'fio-group' });
            // Cor clara para Dark Mode (#e2e8f0)
            const linha = new Konva.Line({
                points: pts, stroke: '#e2e8f0', strokeWidth: 2, 
                lineCap: 'round', lineJoin: 'round', name: 'fio', hitStrokeWidth: 15
            });
            const texto = new Konva.Text({ 
                text: tagCabo, fontSize: 10, fill: '#94a3b8', name: 'tag-fio', fontFamily: 'monospace'
            });

            // Alça
            const handle = new Konva.Circle({ 
                radius: 4, fill: '#3b82f6', stroke: '#fff', strokeWidth: 1, 
                draggable: true, name: 'fio-handle', visible: false 
            });

            handle.on('dragmove', function() {
                const meta = grupo.attrs.metaData;
                const tr = layerWires.getAbsoluteTransform().copy().invert();
                
                // Recalcula posições
                const sComp = layerComponents.findOne('#'+meta.sId);
                const tComp = layerComponents.findOne('#'+meta.tId);
                if(!sComp || !tComp) return;
                
                const p1 = tr.point(sComp.findOne('#'+meta.sBorne).getAbsolutePosition());
                const p2 = tr.point(tComp.findOne('#'+meta.tBorne).getAbsolutePosition());

                let novoOffset;
                if (meta.mode === 'HVH') {
                    novoOffset = this.x();
                    this.y((p1.y + p2.y)/2); 
                } else {
                    novoOffset = this.y();
                    this.x((p1.x + p2.x)/2);
                }
                meta.offset = novoOffset;
                
                const novosPts = calcularRotaOrtogonal(p1.x, p1.y, p2.x, p2.y, meta.mode, novoOffset);
                linha.points(novosPts);
                atualizarTextoEAlca(grupo, novosPts);
            });
            
            handle.dragBoundFunc(function(pos) {
                const mode = grupo.attrs.metaData.mode;
                if (mode === 'HVH') return { x: pos.x, y: this.getAbsolutePosition().y };
                else return { x: this.getAbsolutePosition().x, y: pos.y };
            });

            grupo.add(linha, texto, handle);
            
            grupo.setAttr('metaData', { 
                tag: tagCabo, mode: mode, offset: null,
                sId: wireStartNode.parent.id(), sBorne: wireStartNode.id(),
                tId: borneNode.parent.id(), tBorne: borneNode.id()
            });

            atualizarTextoEAlca(grupo, pts);
            
            grupo.on('click', (e) => { e.cancelBubble = true; if(currentTool==='cursor') selecionarObjeto(grupo); });
            
            layerWires.add(grupo);
            selecionarObjeto(grupo);
            wireStartNode = null;
        }
    }

    function atualizarTextoEAlca(grupo, pts) {
        const handle = grupo.findOne('.fio-handle');
        const txt = grupo.findOne('.tag-fio');
        const mx = (pts[2] + pts[4]) / 2;
        const my = (pts[3] + pts[5]) / 2;
        
        if(handle) handle.position({x: mx, y: my});
        if(txt) txt.position({x: mx, y: my - 15});
    }

    function atualizarCabosConectados(componente) {
        const id = componente.id();
        const tr = layerWires.getAbsoluteTransform().copy().invert();

        layerWires.find('.fio-group').forEach(grupo => {
            const meta = grupo.attrs.metaData;
            if (meta.sId === id || meta.tId === id) {
                const sComp = layerComponents.findOne('#'+meta.sId);
                const tComp = layerComponents.findOne('#'+meta.tId);
                if(sComp && tComp) {
                    const p1 = tr.point(sComp.findOne('#'+meta.sBorne).getAbsolutePosition());
                    const p2 = tr.point(tComp.findOne('#'+meta.tBorne).getAbsolutePosition());
                    const pts = calcularRotaOrtogonal(p1.x, p1.y, p2.x, p2.y, meta.mode, meta.offset);
                    grupo.findOne('.fio').points(pts);
                    atualizarTextoEAlca(grupo, pts);
                }
            }
        });
    }

    // --- SELEÇÃO E PROPRIEDADES UI ---

    function selecionarObjeto(node) {
        // Limpa anteriores
        if(selectedObject) {
            if(selectedObject.hasName('componente')) selectedObject.findOne('.selectionBox')?.hide();
            else if(selectedObject.hasName('fio-group')) {
                selectedObject.findOne('.fio').stroke('#e2e8f0');
                selectedObject.findOne('.fio-handle')?.hide();
            }
        }

        selectedObject = node;
        
        // Atualiza Sidebar de Propriedades
        const propEmpty = document.getElementById('prop-empty');
        const propForm = document.getElementById('prop-form');

        if(node) {
            // Visual
            if(node.hasName('componente')) {
                node.findOne('.selectionBox')?.show();
                preencherSidebar(node.attrs.metaData);
            } else {
                node.findOne('.fio').stroke('#3b82f6'); // Azul seleção
                node.findOne('.fio-handle')?.show();
                preencherSidebar({ ...node.attrs.metaData, fabricante: '---' });
            }
            propEmpty.classList.add('hidden');
            propForm.classList.remove('hidden');
        } else {
            propEmpty.classList.remove('hidden');
            propForm.classList.add('hidden');
        }
    }

    function preencherSidebar(meta) {
        document.getElementById('p-tag').innerText = meta.tag;
        document.getElementById('p-type').innerText = meta.categoria || 'Cabo';
        document.getElementById('in-tag').value = meta.tag;
        
        const fieldManuf = document.getElementById('field-manuf');
        if(meta.categoria) {
            fieldManuf.style.display = 'block';
            document.getElementById('in-manuf').value = meta.fabricante || '';
        } else {
            fieldManuf.style.display = 'none';
        }
    }

    // Função global para salvar do sidebar
    window.aplicarPropriedades = function() {
        if(!selectedObject) return;
        
        const novaTag = document.getElementById('in-tag').value;
        const meta = selectedObject.attrs.metaData;
        meta.tag = novaTag;

        if(selectedObject.hasName('componente')) {
            meta.fabricante = document.getElementById('in-manuf').value;
            selectedObject.findOne('.tagText').text(novaTag);
        } else {
            selectedObject.findOne('.tag-fio').text(novaTag);
        }
        
        selectedObject.setAttr('metaData', meta);
        preencherSidebar(meta); // Atualiza cabeçalho
    }

    window.excluirSelecionado = function() {
        if(selectedObject) {
            selectedObject.destroy();
            selectedObject = null;
            selecionarObjeto(null);
        }
    }

    window.zoomStage = function(delta) {
        const oldScale = stage.scaleX();
        const newScale = Math.max(0.1, oldScale + delta);
        stage.scale({ x: newScale, y: newScale });
        document.getElementById('zoom-level').innerText = Math.round(newScale * 100) + '%';
    }

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') { 
            if(isDrawingWire) { isDrawingWire=false; if(tempWireLine) tempWireLine.destroy(); if(wireStartNode) wireStartNode.fill('transparent'); layerWires.batchDraw(); }
            else selecionarObjeto(null); 
        }
        if (e.key === 'Delete') excluirSelecionado();
    });

    window.addEventListener('resize', () => {
        stage.width(document.getElementById('workspace').offsetWidth);
        stage.height(document.getElementById('workspace').offsetHeight);
    });

</script>
<?= $this->endSection() ?>