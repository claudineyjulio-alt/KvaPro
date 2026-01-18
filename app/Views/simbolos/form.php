<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo ?> - EletCAD</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Ícones do Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        .preview-box {
            width: 100%;
            height: 350px;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
            /* Grade milimetrada simulada */
            background-image: 
                linear-gradient(#e9ecef 1px, transparent 1px), 
                linear-gradient(90deg, #e9ecef 1px, transparent 1px);
            background-size: 10px 10px;
            /* Centralização Absoluta Flexbox */
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            border-radius: 4px;
        }

        .preview-box svg {
            width: 300px; /* Tamanho visual fixo */
            height: 300px;
            border: 1px dashed #dc3545; /* Limite do Viewbox (Vermelho) */
            background: rgba(255, 255, 255, 0.9); /* Fundo branco levemente transparente */
            overflow: visible;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); /* Sombra para destacar do fundo */
        }

        .borne-point {
            fill: #dc3545;
            stroke: white;
            stroke-width: 1px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .borne-point:hover {
            fill: #ffc107;
            stroke: #000;
            r: 5;
        }

        .sigla-tag {
            fill: #0d6efd;
            font-family: Arial, sans-serif;
            font-weight: bold;
            font-size: 14px;
            pointer-events: none;
        }

        /* Cursor de Mira */
        .cursor-mira svg {
            cursor: crosshair !important;
        }
        
        /* Destaque quando o modo de adição está ativo */
        .modo-adicao-ativo {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid mt-4 mb-5">
    
    <form action="<?= base_url('simbolos/salvar') ?>" method="post" id="formSimbolo">
        <input type="hidden" name="id" value="<?= isset($simbolo['id']) ? $simbolo['id'] : '' ?>">
        <input type="hidden" name="bornes_json" id="bornesJsonInput">

        <div class="row">
            
            <!-- ESQUERDA: DADOS -->
            <div class="col-lg-5 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Dados do Símbolo</h5>
                        <a href="<?= base_url('simbolos') ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label">Nome Técnico</label>
                                <input type="text" id="inputNome" name="nome" class="form-control" required placeholder="Ex: Contator Tripolar"
                                       value="<?= isset($simbolo['nome']) ? $simbolo['nome'] : '' ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Sigla</label>
                                <input type="text" id="inputSigla" name="sigla_padrao" class="form-control" required placeholder="K"
                                       value="<?= isset($simbolo['sigla_padrao']) ? $simbolo['sigla_padrao'] : '' ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Categoria</label>
                            <select name="categoria" class="form-select" required>
                                <option value="bobina" <?= (isset($simbolo['categoria']) && $simbolo['categoria'] == 'bobina') ? 'selected' : '' ?>>Bobina</option>
                                <option value="comando" <?= (isset($simbolo['categoria']) && $simbolo['categoria'] == 'comando') ? 'selected' : '' ?>>Contato de Comando</option>
                                <option value="potencia" <?= (isset($simbolo['categoria']) && $simbolo['categoria'] == 'potencia') ? 'selected' : '' ?>>Contato de Potência</option>
                                <option value="conexao" <?= (isset($simbolo['categoria']) && $simbolo['categoria'] == 'conexao') ? 'selected' : '' ?>>Ponto de Conexão</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span>Código SVG</span>
                                <div>
                                    <span class="badge bg-secondary me-1">ViewBox 0-100</span>
                                    <!-- BOTÃO DE PROMPT IA -->
                                    <button type="button" class="btn btn-xs btn-outline-primary py-0" onclick="gerarPromptIA()" title="Copia um pedido pronto para IA">
                                        <i class="bi bi-robot"></i> Prompt p/ IA
                                    </button>
                                </div>
                            </label>
                            <textarea id="svgInput" name="simbolo_svg" class="form-control font-monospace bg-dark text-light" rows="12" 
                                      placeholder='<line x1="50"... /><rect ... />'><?= isset($simbolo['simbolo_svg']) ? $simbolo['simbolo_svg'] : '' ?></textarea>
                            <div class="form-text text-muted d-flex justify-content-between">
                                <span>Cole apenas as formas internas (path, line, rect).</span>
                                <span id="msgCopia" class="text-success fw-bold" style="display:none;">Copiado!</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DIREITA: EDITOR GRÁFICO -->
            <div class="col-lg-7 mb-4">
                
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-eye"></i> Pré-visualização
                        </h6>
                        
                        <!-- SWITCH DE CONTROLE -->
                        <div class="form-check form-switch bg-light px-3 py-1 rounded border">
                            <input class="form-check-input" type="checkbox" id="switchCaptura" style="cursor: pointer;">
                            <label class="form-check-label small fw-bold user-select-none" for="switchCaptura" style="cursor: pointer;">
                                Modo de Captura (Clique no desenho)
                            </label>
                        </div>
                    </div>
                    
                    <div class="card-body p-3"> <!-- Adicionado padding p-3 -->
                        <!-- Aviso Visual -->
                        <div id="msgCaptura" class="alert alert-warning mb-3 py-1 text-center small fw-bold" style="display: none;">
                            <i class="bi bi-crosshair"></i> Clique no desenho onde deseja adicionar o borne
                        </div>

                        <div class="preview-box" id="boxPreview">
                            <svg id="svgPreview" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                <!-- LINHAS DE GUIA CENTRAL (Cruzeta Azul) -->
                                <line x1="50" y1="0" x2="50" y2="100" stroke="#0d6efd" stroke-width="0.5" stroke-dasharray="2" opacity="0.3" pointer-events="none" />
                                <line x1="0" y1="50" x2="100" y2="50" stroke="#0d6efd" stroke-width="0.5" stroke-dasharray="2" opacity="0.3" pointer-events="none" />
                                
                                <g id="layerDesenho"></g>
                                <text id="layerSigla" x="0" y="-5" class="sigla-tag">TAG</text>
                                <g id="layerBornes"></g>
                            </svg>
                        </div>
                        
                        <div class="mt-2 text-muted small d-flex justify-content-between font-monospace">
                            <span>Escala: 100x100mm (Centro: 50,50)</span>
                            <span id="coordDisplay" class="fw-bold text-dark">X: 0.0, Y: 0.0</span>
                        </div>
                    </div>
                </div>

                <!-- TABELA DE BORNES -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                        <h6 class="mb-0"><i class="bi bi-hdd-network"></i> Conexões</h6>
                        
                        <!-- BOTÃO DE AÇÃO -->
                        <button type="button" class="btn btn-sm btn-primary" onclick="ativarModoAdicao()">
                            <i class="bi bi-plus-lg"></i> Adicionar Borne
                        </button>
                    </div>
                    <div class="card-body p-0" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-sm table-striped table-hover mb-0 text-center align-middle">
                            <thead class="table-light sticky-top shadow-sm">
                                <tr>
                                    <th style="width: 20%">ID</th>
                                    <th>Tipo</th>
                                    <th style="width: 15%">X</th>
                                    <th style="width: 15%">Y</th>
                                    <th style="width: 10%">Ação</th>
                                </tr>
                            </thead>
                            <tbody id="listaBornes">
                                <!-- Linhas via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                <button type="submit" class="btn btn-success btn-lg w-100 shadow fw-bold">
                    <i class="bi bi-check-circle-fill"></i> SALVAR COMPONENTE
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // --- DADOS ---
    let bornesData = <?= isset($simbolo['bornes']) && !empty($simbolo['bornes']) ? json_encode($simbolo['bornes']) : '[]' ?>;
    if (typeof bornesData === 'string') { try { bornesData = JSON.parse(bornesData); } catch(e) { bornesData = []; } }

    // --- ELEMENTOS ---
    const inputNome = document.getElementById('inputNome');
    const svgInput = document.getElementById('svgInput');
    const inputSigla = document.getElementById('inputSigla');
    const svgPreview = document.getElementById('svgPreview');
    const boxPreview = document.getElementById('boxPreview');
    const layerDesenho = document.getElementById('layerDesenho');
    const layerSigla = document.getElementById('layerSigla');
    const layerBornes = document.getElementById('layerBornes');
    const listaBornes = document.getElementById('listaBornes');
    const hiddenInput = document.getElementById('bornesJsonInput');
    const switchCaptura = document.getElementById('switchCaptura');
    const coordDisplay = document.getElementById('coordDisplay');
    const msgCaptura = document.getElementById('msgCaptura');
    const msgCopia = document.getElementById('msgCopia');

    // --- GERADOR DE PROMPT IA ---
    function gerarPromptIA() {
        const nome = inputNome.value || "Componente Elétrico";
        const prompt = `Create the clean SVG code for an electrical symbol of: ${nome}.
Strict Rules:
1. DO NOT include the <svg> tag, only the inner shapes (path, circle, rect, line).
2. The drawing MUST be centered at coordinates X=50, Y=50.
3. It must fit perfectly within a 100x100 coordinate system (ViewBox 0 0 100 100).
4. Use stroke="black" and stroke-width="2". Use fill="none" unless solid.
5. Example output: <circle cx="50" cy="50" r="20" stroke="black" stroke-width="2" fill="none" />`;

        navigator.clipboard.writeText(prompt).then(() => {
            msgCopia.style.display = 'inline';
            setTimeout(() => { msgCopia.style.display = 'none'; }, 3000);
        });
    }

    // --- RENDERIZAÇÃO ---

    function updateDrawing() {
        let rawHtml = svgInput.value;
        rawHtml = rawHtml.replace(/<\/?svg[^>]*>/g, "");
        layerDesenho.innerHTML = rawHtml;
    }

    function updateSigla() {
        layerSigla.textContent = inputSigla.value.trim() !== "" ? inputSigla.value.trim() : "TAG";
    }

    function renderBornes() {
        listaBornes.innerHTML = '';
        layerBornes.innerHTML = '';

        bornesData.forEach((borne, index) => {
            // Tabela
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" class="form-control form-control-sm text-center fw-bold" value="${borne.id}" oninput="updateBorneData(${index}, 'id', this.value)" placeholder="A1"></td>
                <td>
                    <select class="form-select form-select-sm" onchange="updateBorneData(${index}, 'tipo', this.value)">
                        <option value="comando" ${borne.tipo === 'comando' ? 'selected' : ''}>Comando</option>
                        <option value="potencia" ${borne.tipo === 'potencia' ? 'selected' : ''}>Potência</option>
                    </select>
                </td>
                <td><input type="number" step="0.1" class="form-control form-control-sm text-center" value="${borne.x}" oninput="updateBorneData(${index}, 'x', this.value)"></td>
                <td><input type="number" step="0.1" class="form-control form-control-sm text-center" value="${borne.y}" oninput="updateBorneData(${index}, 'y', this.value)"></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeBorne(${index})"><i class="bi bi-trash-fill"></i></button></td>
            `;
            listaBornes.appendChild(tr);

            // SVG
            const group = document.createElementNS("http://www.w3.org/2000/svg", "g");
            const circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
            circle.setAttribute("cx", borne.x);
            circle.setAttribute("cy", borne.y);
            circle.setAttribute("r", "2.5");
            circle.setAttribute("class", "borne-point");
            
            const text = document.createElementNS("http://www.w3.org/2000/svg", "text");
            text.setAttribute("x", parseFloat(borne.x) + 4);
            text.setAttribute("y", parseFloat(borne.y) + 3);
            text.setAttribute("font-size", "7");
            text.setAttribute("font-family", "Arial");
            text.setAttribute("fill", "#dc3545");
            text.textContent = borne.id;

            circle.addEventListener('dblclick', function(e) {
                e.stopPropagation();
                if(confirm(`Excluir borne "${borne.id}"?`)) removeBorne(index);
            });

            group.appendChild(circle);
            group.appendChild(text);
            layerBornes.appendChild(group);
        });

        hiddenInput.value = JSON.stringify(bornesData);
    }

    // --- AÇÕES ---

    // Função chamada pelo botão "Adicionar Borne"
    function ativarModoAdicao() {
        switchCaptura.checked = true;
        alternarModoVisual(true);
        boxPreview.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function removeBorne(index) {
        bornesData.splice(index, 1);
        renderBornes();
    }

    window.updateBorneData = function(index, field, value) {
        bornesData[index][field] = value;
        renderBornes();
    }

    // --- LÓGICA DE CAPTURA ---

    function alternarModoVisual(ativo) {
        if(ativo) {
            boxPreview.classList.add('cursor-mira', 'modo-adicao-ativo');
            msgCaptura.style.display = 'block';
        } else {
            boxPreview.classList.remove('cursor-mira', 'modo-adicao-ativo');
            msgCaptura.style.display = 'none';
        }
    }

    switchCaptura.addEventListener('change', function() {
        alternarModoVisual(this.checked);
    });

    function getSVGPoint(event) {
        let pt = svgPreview.createSVGPoint();
        pt.x = event.clientX;
        pt.y = event.clientY;
        let svgP = pt.matrixTransform(svgPreview.getScreenCTM().inverse());
        return {
            x: Math.round(svgP.x * 10) / 10,
            y: Math.round(svgP.y * 10) / 10
        };
    }

    svgPreview.addEventListener('mousemove', function(e) {
        let p = getSVGPoint(e);
        coordDisplay.textContent = `X: ${p.x.toFixed(1)}, Y: ${p.y.toFixed(1)}`;
    });

    // O CLIQUE MÁGICO
    svgPreview.addEventListener('click', function(e) {
        if (!switchCaptura.checked) return;

        let p = getSVGPoint(e);
        let novoId = (bornesData.length + 1).toString();
        
        bornesData.push({ 
            id: novoId, 
            tipo: 'comando', 
            x: p.x, 
            y: p.y 
        });

        renderBornes();
    });

    // --- INIT ---
    svgInput.addEventListener('input', updateDrawing);
    inputSigla.addEventListener('input', updateSigla);
    updateDrawing();
    updateSigla();
    renderBornes();

</script>

</body>
</html>