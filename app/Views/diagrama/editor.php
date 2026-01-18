<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor EletCAD - Diagrama</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://unpkg.com/konva@9.2.0/konva.min.js"></script>

    <style>
        body { margin: 0; padding: 0; overflow: hidden; background-color: #e9ecef; }
        .editor-container { display: flex; height: 100vh; }
        
        /* Sidebar */
        .sidebar { width: 260px; background: white; border-right: 1px solid #dee2e6; display: flex; flex-direction: column; z-index: 20; }
        .sidebar-header { padding: 12px; background: #212529; color: white; display: flex; align-items: center; justify-content: space-between; }
        .component-list { flex: 1; overflow-y: auto; padding: 10px; }
        .symbol-card { cursor: pointer; border: 1px solid #dee2e6; margin-bottom: 8px; transition: 0.2s; }
        .symbol-card:hover { border-color: #d62828; background-color: #fff5f5; }
        .symbol-preview svg { width: 32px; height: 32px; pointer-events: none; }

        /* Canvas */
        .canvas-wrapper { flex: 1; position: relative; background-color: #e9ecef; 
            background-image: linear-gradient(#dee2e6 1px, transparent 1px), linear-gradient(90deg, #dee2e6 1px, transparent 1px);
            background-size: 20px 20px; }
        
        .toolbar {
            position: absolute; top: 15px; left: 50%; transform: translateX(-50%);
            background: white; padding: 8px 15px; border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15); display: flex; gap: 10px; z-index: 100;
        }
        .tool-btn {
            width: 40px; height: 40px; border-radius: 5px; border: 1px solid #dee2e6;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; background: #f8f9fa; color: #555; transition: 0.2s;
        }
        .tool-btn:hover { background: #e9ecef; }
        .tool-btn.active { background: #d62828; color: white; border-color: #d62828; }
        .cursor-crosshair { cursor: crosshair !important; }
    </style>
</head>
<body>

<div class="editor-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <span class="fw-bold"><i class="bi bi-lightning-charge"></i> EletCAD</span>
            <a href="<?= base_url('dashboard') ?>" class="text-white text-decoration-none"><i class="bi bi-x-lg"></i></a>
        </div>
        <div class="p-2 border-bottom"><input type="text" class="form-control form-control-sm" placeholder="Buscar..."></div>
        <div class="component-list">
            <?php foreach ($simbolosPorCategoria as $categoria => $simbolos): ?>
                <small class="text-uppercase text-muted fw-bold mt-2 d-block px-1" style="font-size: 0.7rem;"><?= $categoria ?></small>
                <?php foreach ($simbolos as $s): ?>
                    <div class="card symbol-card" onclick='selecionarFerramentaInsercao(<?= json_encode($s) ?>)'>
                        <div class="card-body p-2 d-flex align-items-center">
                            <div class="symbol-preview me-2 border rounded p-1">
                                <svg viewBox="0 0 100 100">
                                    <?= !empty($s['simbolo_svg']) ? preg_replace('/<\/?svg[^>]*>/i', '', $s['simbolo_svg']) : '' ?>
                                </svg>
                            </div>
                            <div style="line-height: 1.1;">
                                <div class="fw-bold small text-dark"><?= $s['nome'] ?></div>
                                <small class="text-muted" style="font-size: 0.7rem;">Tag: <?= $s['sigla_padrao'] ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="canvas-wrapper" id="canvasWrapper">
        <div class="toolbar">
            <div class="tool-btn active" id="btnCursor" onclick="setTool('cursor')" title="Seleção (V)">
                <i class="bi bi-cursor-fill"></i>
            </div>
            <div class="tool-btn" id="btnCabo" onclick="setTool('cabo')" title="Cabo (C)">
                <i class="bi bi-bezier2"></i>
            </div>
            <div class="vr mx-1"></div>
            <div class="tool-btn text-danger" onclick="excluirSelecionado()" title="Excluir (Del)">
                <i class="bi bi-trash"></i>
            </div>
        </div>
        <div id="container"></div>
    </div>
</div>

<!-- MODAL PROPRIEDADES -->
<div class="modal fade" id="modalPropriedades" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header py-2 bg-light">
                <h6 class="modal-title fw-bold">Propriedades</h6>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="propId">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Identificação (TAG)</label>
                    <input type="text" class="form-control form-control-sm fw-bold" id="propTag" autocomplete="off">
                </div>
                <div id="divVinculo" class="mb-3" style="display:none; background-color: #f8f9fa; padding: 8px; border-radius: 4px; border: 1px dashed #dee2e6;">
                    <label class="form-label small fw-bold text-primary mb-1">🔗 Vincular a (Pai)</label>
                    <select class="form-select form-select-sm" id="propVinculo">
                        <option value="">(Sem vínculo)</option>
                    </select>
                    <small class="text-muted" style="font-size: 0.65rem;">Herda a TAG do componente pai.</small>
                </div>
                <div id="campoFab" class="mb-2">
                    <label class="form-label small fw-bold text-muted">Fabricante / Modelo</label>
                    <input type="text" class="form-control form-control-sm" id="propFab" placeholder="Ex: WEG CWB">
                </div>
            </div>
            <div class="modal-footer py-2 bg-light">
                <button type="button" class="btn btn-sm btn-primary w-100" onclick="salvarPropriedades()">
                    <i class="bi bi-check-lg"></i> Aplicar Alterações
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // --- SETUP INICIAL ---
    const width = document.getElementById('canvasWrapper').offsetWidth;
    const height = document.getElementById('canvasWrapper').offsetHeight;
    const contadores = { componentes: {}, fios: 0 }; 
    let currentTool = 'cursor'; 
    let simboloParaInserir = null;
    let selectedObject = null; 
    let isDrawingWire = false;
    let wireStartNode = null; 
    let tempWireLine = null;  

    const stage = new Konva.Stage({
        container: 'container', width: width, height: height, draggable: true
    });

    const layerGrid = new Konva.Layer();
    const layerWires = new Konva.Layer(); 
    const layerComponents = new Konva.Layer(); 
    const layerUI = new Konva.Layer(); // UI fica por cima de tudo
    stage.add(layerGrid, layerWires, layerComponents, layerUI);

    // --- FERRAMENTAS ---
    function setTool(toolName) {
        currentTool = toolName;
        simboloParaInserir = null;
        
        document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove('active'));
        if(toolName === 'cursor') document.getElementById('btnCursor').classList.add('active');
        if(toolName === 'cabo') document.getElementById('btnCabo').classList.add('active');

        const wrapper = document.getElementById('canvasWrapper');
        wrapper.className = 'canvas-wrapper';
        if (toolName === 'cabo') wrapper.classList.add('cursor-crosshair');
        
        selecionarObjeto(null);
    }

    function selecionarFerramentaInsercao(dados) {
        setTool('inserir');
        simboloParaInserir = dados;
    }

    // --- EVENTOS GLOBAIS ---
    stage.on('click tap', function (e) {
        if (e.target === stage) {
            selecionarObjeto(null);
            if (currentTool === 'inserir' && simboloParaInserir) {
                const pos = stage.getRelativePointerPosition();
                const snapX = Math.round(pos.x / 10) * 10;
                const snapY = Math.round(pos.y / 10) * 10;
                criarComponente(simboloParaInserir, snapX, snapY);
                // setTool('cursor'); 
            }
            return;
        }

        if (e.target.hasName('fio-handle')) return;

        let group = e.target.findAncestor('.componente');
        if (group && currentTool === 'cursor') {
            selecionarObjeto(group);
            return;
        }

        if (e.target.hasName('fio') && currentTool === 'cursor') {
            selecionarObjeto(e.target.parent); 
        }
    });

    // --- COMPONENTES ---
    function criarComponente(dados, x, y) {
        const prefixo = dados.sigla_padrao || 'X';
        let tagFinal = '';

        if (dados.categoria === 'bobina') {
            if (!contadores.componentes[prefixo]) contadores.componentes[prefixo] = 0;
            contadores.componentes[prefixo]++;
            tagFinal = `-${prefixo}${contadores.componentes[prefixo]}`;
        } else {
            tagFinal = `-${prefixo}?`;
        }

        const group = new Konva.Group({
            x: x, y: y, draggable: true,
            name: 'componente',
            id: 'comp_' + Date.now(),
            metaData: { tag: tagFinal, fabricante: '', categoria: dados.categoria, masterId: null }
        });

        // Parse Seguro do SVG
        let svgCode = dados.simbolo_svg;
        if(!svgCode) svgCode = '<rect x="10" y="10" width="80" height="80" stroke="red"/>';
        svgCode = svgCode.replace(/<\/?svg[^>]*>/g, ""); 

        const svgString = `<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">${svgCode}</svg>`;
        const blob = new Blob([svgString], {type: 'image/svg+xml'});
        const url = URL.createObjectURL(blob);
        const image = new Image();
        
        image.onload = () => {
            const kImage = new Konva.Image({
                image: image, width: 100, height: 100, offsetX: 50, offsetY: 50
            });
            group.add(kImage);
            kImage.moveToBottom();
            
            const selectionBox = new Konva.Rect({
                x: -50, y: -50, width: 100, height: 100,
                stroke: 'red', strokeWidth: 2, dash: [5, 5],
                visible: false, name: 'selectionBox'
            });
            group.add(selectionBox);
        };
        image.src = url;

        const text = new Konva.Text({
            text: tagFinal,
            x: -160, y: -10, width: 100, align: 'right',
            fontSize: 14, fontFamily: 'Arial', fill: '#333', fontStyle: 'bold',
            name: 'tagText'
        });
        group.add(text);

        let bornes = dados.bornes;
        if (typeof bornes === 'string') { try { bornes = JSON.parse(bornes); } catch(e) { bornes = []; } }

        if (bornes && Array.isArray(bornes)) {
            bornes.forEach(b => {
                const bx = parseFloat(b.x) - 50;
                const by = parseFloat(b.y) - 50;

                const borneCircle = new Konva.Circle({
                    x: bx, y: by, radius: 5,
                    fill: 'transparent', name: 'borne', id: b.id
                });

                borneCircle.on('mouseenter', function() { if(currentTool === 'cabo') this.fill('#28a745'); });
                borneCircle.on('mouseleave', function() { if(!isDrawingWire || wireStartNode !== this) this.fill('transparent'); });
                borneCircle.on('click', function(e) {
                    if (currentTool === 'cabo') {
                        e.cancelBubble = true;
                        iniciarOuTerminarCabo(this);
                    }
                });
                group.add(borneCircle);
            });
        }

        group.on('dblclick', function(e) { e.cancelBubble = true; abrirModalPropriedades(this, 'componente'); });
        
        // ANCORAGEM: Atualiza cabos ao mover componente
        group.on('dragmove', function() {
            // Snap to Grid 10px
            this.x(Math.round(this.x() / 10) * 10);
            this.y(Math.round(this.y() / 10) * 10);
            atualizarCabosConectados(this);
        });

        layerComponents.add(group);
    }

    // --- 5. CABOS COM PONTOS PERSISTENTES E DIVISÃO AUTOMÁTICA ---

    function iniciarOuTerminarCabo(borneNode) {
        const absPos = borneNode.getAbsolutePosition();
        const tr = layerWires.getAbsoluteTransform().copy().invert();
        const localPos = tr.point(absPos);

        if (!isDrawingWire) {
            isDrawingWire = true; wireStartNode = borneNode;
            tempWireLine = new Konva.Line({ points: [localPos.x, localPos.y, localPos.x, localPos.y], stroke: '#999', strokeWidth: 2, dash: [4, 4] });
            layerWires.add(tempWireLine);
        } else {
            isDrawingWire = false;
            if(tempWireLine) tempWireLine.destroy();
            wireStartNode.fill('transparent');

            const startPos = tr.point(wireStartNode.getAbsolutePosition());
            
            // Rota Inicial Manhattan (3 segmentos)
            const dx = Math.abs(startPos.x - localPos.x);
            const dy = Math.abs(startPos.y - localPos.y);
            let pts;

            if (dx > dy) {
                const mx = (startPos.x + localPos.x) / 2;
                pts = [startPos.x, startPos.y, mx, startPos.y, mx, localPos.y, localPos.x, localPos.y];
            } else {
                const my = (startPos.y + localPos.y) / 2;
                pts = [startPos.x, startPos.y, startPos.x, my, localPos.x, my, localPos.x, localPos.y];
            }

            contadores.fios++;
            const tagCabo = 'W' + contadores.fios;

            const grupoCabo = new Konva.Group({ name: 'fio-group' });
            
            const linhaReal = new Konva.Line({
                points: pts, stroke: 'black', strokeWidth: 2, lineCap: 'round', lineJoin: 'round', name: 'fio', hitStrokeWidth: 15
            });

            const textoCabo = new Konva.Text({ text: tagCabo, fontSize: 10, fontFamily: 'Arial', fill: '#666', name: 'tag-fio' });

            grupoCabo.add(linhaReal, textoCabo);
            
            grupoCabo.setAttr('metaData', { 
                tag: tagCabo, 
                sId: wireStartNode.parent.id(), sBorne: wireStartNode.id(),
                tId: borneNode.parent.id(), tBorne: borneNode.id(),
                points: pts // Salva os pontos fixos
            });

            // Gera alças
            gerarAlcas(grupoCabo);
            atualizarTextoCabo(grupoCabo);

            grupoCabo.on('click', (e) => { e.cancelBubble = true; if(currentTool==='cursor') selecionarObjeto(grupoCabo); });
            grupoCabo.on('dblclick', (e) => { e.cancelBubble = true; abrirModalPropriedades(grupoCabo, 'fio'); });

            layerWires.add(grupoCabo);
            selecionarObjeto(grupoCabo);
            wireStartNode = null;
        }
    }

    function gerarAlcas(grupoCabo) {
        // Limpa anteriores na layer UI
        layerUI.find(node => node.attrs.parentCabo === grupoCabo.id()).forEach(h => h.destroy());
        
        const linha = grupoCabo.findOne('.fio');
        const pts = linha.points();
        
        for(let i=0; i < pts.length - 2; i+=2) {
            const x1 = pts[i], y1 = pts[i+1];
            const x2 = pts[i+2], y2 = pts[i+3];
            
            const isH = Math.abs(y1 - y2) < 0.1;
            const isV = Math.abs(x1 - x2) < 0.1;
            
            // Ignora diagonais
            if (!isH && !isV) continue;

            const mx = (x1+x2)/2;
            const my = (y1+y2)/2;

            const handle = new Konva.Circle({
                x: mx, y: my, radius: 4, fill: '#0d6efd', stroke: 'white', strokeWidth: 1,
                draggable: true, name: 'fio-handle',
                parentCabo: grupoCabo.id(), 
                segmentIdx: i
            });

            // Lógica de "Split on Drag": Se for o primeiro ou ultimo segmento, divide a linha
            const isFirst = (i === 0);
            const isLast = (i === pts.length - 4);

            handle.on('dragstart', function() {
                // Se arrastar ponta, cria cotovelo novo
                if(isFirst || isLast) {
                    const idx = this.attrs.segmentIdx;
                    const oldPts = grupoCabo.attrs.metaData.points;
                    let newPts;

                    if(isFirst) {
                        // Duplica inicio: [x0, y0, x0, y0, x1, y1...]
                        newPts = [oldPts[0], oldPts[1], oldPts[0], oldPts[1], ...oldPts.slice(2)];
                        // Atualiza alça atual para controlar o NOVO segmento (index + 2)
                        this.attrs.segmentIdx += 2;
                        // Atualiza outras alças para frente
                        layerUI.find(h => h.attrs.parentCabo === grupoCabo.id() && h !== this).forEach(h => {
                            if(h.attrs.segmentIdx >= 0) h.attrs.segmentIndex += 2;
                        });
                    } else {
                        // Duplica fim
                        const len = oldPts.length;
                        newPts = [...oldPts.slice(0, len-2), oldPts[len-2], oldPts[len-1], oldPts[len-2], oldPts[len-1]];
                    }
                    
                    grupoCabo.findOne('.fio').points(newPts);
                    grupoCabo.attrs.metaData.points = newPts;
                }
            });

            handle.dragBoundFunc(function(pos) {
                if(isH) return { x: this.absolutePosition().x, y: pos.y }; 
                else return { x: pos.x, y: this.absolutePosition().y };
            });

            handle.on('dragmove', function() {
                const idx = this.attrs.segmentIdx;
                const newPts = grupoCabo.findOne('.fio').points().slice();
                
                if(isH) {
                    newPts[idx+1] = this.y(); newPts[idx+3] = this.y();
                } else {
                    newPts[idx] = this.x(); newPts[idx+2] = this.x();
                }
                
                grupoCabo.findOne('.fio').points(newPts);
                grupoCabo.attrs.metaData.points = newPts;
                atualizarTextoCabo(grupoCabo);
            });
            
            handle.on('dragend', () => { gerarAlcas(grupoCabo); }); 

            layerUI.add(handle);
        }
        layerUI.batchDraw();
    }

    function atualizarTextoCabo(grupoCabo) {
        const pts = grupoCabo.attrs.metaData.points;
        const txt = grupoCabo.findOne('.tag-fio');
        const mid = Math.floor(pts.length/4)*2;
        if(pts.length >= 4) {
            txt.position({ x: (pts[mid] + pts[mid+2])/2, y: (pts[mid+1] + pts[mid+3])/2 - 15 });
        }
    }

    // --- ANCORAGEM ---
    function atualizarCabosConectados(componente) {
        const compId = componente.id();
        const tr = layerWires.getAbsoluteTransform().copy().invert();

        layerWires.find('.fio-group').forEach(grupoCabo => {
            const meta = grupoCabo.attrs.metaData;
            let points = meta.points; 
            let changed = false;

            if (meta.sId === compId) {
                const sComp = layerComponents.findOne('#'+meta.sId);
                if(sComp) {
                    const p = tr.point(sComp.findOne('#'+meta.sBorne).getAbsolutePosition());
                    const oldX = points[0]; const oldY = points[1];
                    points[0] = p.x; points[1] = p.y;
                    
                    // Mantém ortogonalidade simples
                    if (Math.abs(oldX - points[2]) < 1) points[2] = p.x;
                    else if (Math.abs(oldY - points[3]) < 1) points[3] = p.y;
                    
                    changed = true;
                }
            }

            if (meta.tId === compId) {
                const tComp = layerComponents.findOne('#'+meta.tId);
                if(tComp) {
                    const p = tr.point(tComp.findOne('#'+meta.tBorne).getAbsolutePosition());
                    const last = points.length - 2;
                    const oldX = points[last]; const oldY = points[last+1];
                    points[last] = p.x; points[last+1] = p.y;

                    if (Math.abs(oldX - points[last-2]) < 1) points[last-2] = p.x;
                    else if (Math.abs(oldY - points[last-1]) < 1) points[last-1] = p.y;
                    
                    changed = true;
                }
            }

            if(changed) {
                grupoCabo.findOne('.fio').points(points);
                atualizarTextoCabo(grupoCabo);
                if(selectedObject === grupoCabo) gerarAlcas(grupoCabo);
            }
        });
    }

    // Preview
    stage.on('mousemove', function() {
        if (!isDrawingWire || !tempWireLine) return;
        const pointer = stage.getRelativePointerPosition();
        const tr = layerWires.getAbsoluteTransform().copy().invert();
        const startPos = tr.point(wireStartNode.getAbsolutePosition());
        const pts = calcularRotaOrtogonal(startPos.x, startPos.y, pointer.x, pointer.y);
        tempWireLine.points(pts);
        layerWires.batchDraw();
    });

    function calcularRotaOrtogonal(x1, y1, x2, y2) {
        const meioX = (x1+x2)/2;
        return [x1, y1, meioX, y1, meioX, y2, x2, y2];
    }

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') { 
            if(isDrawingWire) { isDrawingWire=false; if(tempWireLine) tempWireLine.destroy(); if(wireStartNode) wireStartNode.fill('transparent'); layerWires.batchDraw(); }
            else selecionarObjeto(null); 
        }
        if (e.key === 'Delete') excluirSelecionado();
    });

    function selecionarObjeto(node) {
        layerUI.find('.fio-handle').forEach(h => h.destroy());
        if (selectedObject) {
            if (selectedObject.hasName('componente')) selectedObject.findOne('.selectionBox')?.hide();
            else if (selectedObject.hasName('fio-group')) selectedObject.findOne('.fio').stroke('black');
        }
        selectedObject = node;
        if (node) {
            if (node.hasName('componente')) node.findOne('.selectionBox')?.show();
            else if (node.hasName('fio-group')) {
                node.findOne('.fio').stroke('red');
                gerarAlcas(node);
            }
        }
        layerUI.batchDraw();
    }

    function excluirSelecionado() { if(selectedObject) { selectedObject.destroy(); selectedObject = null; layerUI.batchDraw(); } }

    let modalPropriedades = null; let objEditando = null;
    document.addEventListener('DOMContentLoaded', () => { modalPropriedades = new bootstrap.Modal(document.getElementById('modalPropriedades')); });

    function abrirModalPropriedades(node, tipo) {
        objEditando = node;
        const meta = node.attrs.metaData || {};
        document.getElementById('propTag').value = meta.tag || '';
        const div = document.getElementById('divVinculo'), sel = document.getElementById('propVinculo');
        div.style.display = 'none'; sel.innerHTML = '<option value="">(Sem vínculo)</option>';

        if(tipo === 'fio') { document.getElementById('campoFab').style.display = 'none'; } 
        else {
            document.getElementById('campoFab').style.display = 'block';
            document.getElementById('propFab').value = meta.fabricante || '';
            if (meta.categoria === 'comando' || meta.categoria === 'potencia') {
                div.style.display = 'block';
                layerComponents.find('.componente').filter(c => c.attrs.metaData?.categoria === 'bobina').forEach(b => {
                    const opt = document.createElement('option'); opt.value = b.id(); opt.text = b.attrs.metaData.tag;
                    if(meta.masterId === b.id()) opt.selected = true;
                    sel.appendChild(opt);
                });
            }
        }
        if(modalPropriedades) modalPropriedades.show();
    }

    function salvarPropriedades() {
        if (!objEditando) return;
        let novaTag = document.getElementById('propTag').value;
        const meta = objEditando.attrs.metaData;
        if(objEditando.hasName('componente')) {
            meta.fabricante = document.getElementById('propFab').value;
            const vinculo = document.getElementById('propVinculo').value;
            if (vinculo) { const m = layerComponents.findOne('#'+vinculo); if(m) { novaTag = m.attrs.metaData.tag; meta.masterId = vinculo; } } else { meta.masterId = null; }
            objEditando.findOne('.tagText').text(novaTag);
        } else if(objEditando.hasName('fio-group')) { objEditando.findOne('.tag-fio').text(novaTag); }
        meta.tag = novaTag; objEditando.setAttr('metaData', meta);
        if(modalPropriedades) modalPropriedades.hide();
    }

    window.addEventListener('resize', () => {
        stage.width(document.getElementById('canvasWrapper').offsetWidth);
        stage.height(document.getElementById('canvasWrapper').offsetHeight);
    });
</script>

</body>
</html>