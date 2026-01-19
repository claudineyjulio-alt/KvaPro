<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Projeto - KvaPro</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
          theme: { extend: { colors: { eletblue: { DEFAULT: '#0f2649', dark: '#0a1a33', light: '#eef2ff' } }, fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans text-eletgray min-h-screen pb-10">

    <nav class="bg-eletblue text-white p-4 shadow-md sticky top-0 z-50">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="<?= base_url('assets/img/logo.png') ?>" alt="KvaPro" class="h-10 bg-white rounded p-1">
                <span class="font-bold text-xl tracking-tight hidden md:inline">Novo Projeto</span>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="abrirModalImportacao()" class="text-sm bg-green-600 hover:bg-green-500 text-white font-bold px-4 py-2 rounded shadow transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                    Importar .kva
                </button>
                <a href="<?= base_url('dashboard') ?>" class="text-sm bg-white/10 hover:bg-white/20 px-3 py-2 rounded transition">Voltar</a>
            </div>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto mt-8 px-4">
        <form id="form-projeto" action="<?= base_url('projeto/salvar') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <span class="bg-eletblue text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">2</span>
                    <h2 class="font-semibold text-gray-700">Identificação do Projeto</h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título da Obra *</label>
                        <input type="text" name="titulo_obra" id="titulo_obra" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-eletblue p-2 border">
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Cliente *</label>
                        <input type="text" name="cliente_nome" id="cliente_nome" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-eletblue p-2 border">
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Obra</label>
                        <select name="tipo_obra" id="tipo_obra" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-eletblue p-2 border bg-white">
                            <option value="Nova Ligacao">Nova Ligação</option>
                            <option value="Aumento de Carga">Aumento de Carga</option>
                            <option value="Reforma">Reforma</option>
                            <option value="Provisoria">Ligação Provisória</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 border-t border-gray-100 pt-4 mt-2">
                         <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Localização</h3>
                         <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                            <div class="md:col-span-1"><label class="text-xs text-gray-600">CEP</label><input type="text" id="cep" name="cep" maxlength="9" class="w-full border p-2 rounded"></div>
                            <div class="md:col-span-4"><label class="text-xs text-gray-600">Logradouro</label><input type="text" id="logradouro" name="logradouro" class="w-full border p-2 rounded"></div>
                            <div class="md:col-span-1"><label class="text-xs text-gray-600">Nº</label><input type="text" id="numero" name="numero" class="w-full border p-2 rounded"></div>
                            <div class="md:col-span-2"><label class="text-xs text-gray-600">Bairro</label><input type="text" id="bairro" name="bairro" class="w-full border p-2 rounded"></div>
                            <div class="md:col-span-2"><label class="text-xs text-gray-600">Cidade</label><input type="text" id="cidade" name="cidade" class="w-full border p-2 rounded"></div>
                            <div class="md:col-span-1"><label class="text-xs text-gray-600">UF</label><input type="text" id="uf" name="uf" class="w-full border p-2 rounded uppercase"></div>
                            <div class="md:col-span-1"><label class="text-xs text-gray-600">Zona</label><select name="zona" id="zona" class="w-full border p-2 rounded bg-white"><option value="Urbano">Urbana</option><option value="Rural">Rural</option></select></div>
                         </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <span class="bg-eletblue text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">3</span>
                    <h2 class="font-semibold text-gray-700">Parâmetros da Concessionária</h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Concessionária *</label>
                        <select name="concessionaria_id" id="concessionaria_id" onchange="atualizarDimensionamentoGlobal()" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-eletblue p-2 border bg-white">
                            <option value="">Selecione...</option>
                            <?php foreach($concessionarias as $con): ?>
                                <option value="<?= $con['id'] ?>"><?= $con['nome'] ?> - <?= $con['estado'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tensão de Atendimento *</label>
                        <select name="tensao_id" id="tensao_id" onchange="atualizarDimensionamentoGlobal()" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-eletblue p-2 border bg-white">
                            <option value="">Selecione...</option>
                            <?php foreach($tensoes as $ten): ?>
                                <option value="<?= $ten['id'] ?>">[<?= $ten['classe'] ?>] <?= $ten['descricao'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Ramal</label>
                        <select name="tipo_ramal" id="tipo_ramal" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-eletblue p-2 border bg-white">
                            <option value="Aereo">Aéreo</option>
                            <option value="Subterraneo">Subterrâneo</option>
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Localização Medidor</label>
                        <select name="localizacao_medidor" id="localizacao_medidor" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-eletblue p-2 border bg-white">
                            <option value="Muro Frontal">Muro Frontal</option>
                            <option value="Poste Auxiliar">Poste Auxiliar</option>
                            <option value="Fachada">Fachada</option>
                            <option value="Pontalete">Pontalete</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="bg-eletblue text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">4</span>
                        <h2 class="font-semibold text-gray-700">Adicionar Medições</h2>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-5 gap-4 bg-blue-50/30">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Classe</label>
                        <select id="add_classe" class="w-full border-gray-300 rounded text-sm p-2 bg-white">
                            <option value="Residencial">Residencial</option>
                            <option value="Comercial">Comercial</option>
                            <option value="Industrial">Industrial</option>
                            <option value="B. Incendio">Bomba Incêndio</option>
                            <option value="Condominio">Condomínio</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Categoria</label>
                        <select id="add_categoria" class="w-full border-gray-300 rounded text-sm p-2 bg-white" onchange="filtrarDisjuntoresLocal()">
                            <option value="M">Monofásica</option>
                            <option value="B">Bifásica</option>
                            <option value="T">Trifásica</option>
                            <option value="Calcular">A Calcular</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Padrão / Disjuntor</label>
                        <select id="add_disjuntor" class="w-full border-gray-300 rounded text-sm p-2 bg-white">
                            <option value="">Aguardando Passo 3...</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Qtd. Unidades</label>
                        <div class="flex gap-2">
                            <input type="number" id="add_qtd" value="1" min="1" class="w-full border-gray-300 rounded text-sm p-2">
                            <button type="button" onclick="adicionarNaLista()" class="bg-eletblue text-white px-3 py-2 rounded hover:bg-blue-900 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="px-6 pb-6">
                    <h3 class="text-xs font-bold text-gray-500 uppercase mt-6 mb-3 border-b pb-2">Lista de Unidades (Edite os detalhes abaixo)</h3>
                    
                    <div id="lista-unidades" class="space-y-3">
                        <div id="empty-state" class="text-center py-8 text-gray-400 text-sm italic bg-gray-50 rounded-lg border border-dashed border-gray-200">
                            Nenhuma medição adicionada ainda. Use o painel acima.
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 pb-12">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition duration-200 flex items-center gap-2 transform hover:scale-105">
                    Finalizar e Baixar Projeto
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                </button>
            </div>
        </form>
    </div>

    <div id="modal-importacao" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[60] flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 text-center">Importar Projeto .kva</h3>
            <input type="file" id="file-upload" accept=".kva,.json" class="block w-full text-sm text-gray-500 file:bg-eletblue file:text-white file:rounded-full file:px-4 file:py-2 hover:file:bg-blue-900 border border-gray-200 rounded-lg mb-4"/>
            <div class="flex gap-3 justify-end">
                <button onclick="fecharModalImportacao()" class="px-4 py-2 text-gray-600">Cancelar</button>
                <button onclick="processarImportacao()" class="bg-green-600 text-white font-bold px-6 py-2 rounded-lg">Carregar</button>
            </div>
        </div>
    </div>

    <div id="modal-editar" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[70] flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-eletblue" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                Editar Medição
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Classe</label>
                    <select id="edit_classe" class="w-full border-gray-300 rounded text-sm p-2 bg-white">
                        <option value="Residencial">Residencial</option>
                        <option value="Comercial">Comercial</option>
                        <option value="Industrial">Industrial</option>
                        <option value="B. Incendio">Bomba Incêndio</option>
                        <option value="Condominio">Condomínio</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Categoria</label>
                    <select id="edit_categoria" class="w-full border-gray-300 rounded text-sm p-2 bg-white" onchange="filtrarDisjuntoresModal()">
                        <option value="M">Monofásica</option>
                        <option value="B">Bifásica</option>
                        <option value="T">Trifásica</option>
                        <option value="Calcular">A Calcular</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Padrão / Disjuntor</label>
                    <select id="edit_disjuntor" class="w-full border-gray-300 rounded text-sm p-2 bg-white">
                        </select>
                </div>
            </div>

            <div class="flex gap-3 justify-end mt-6">
                <button type="button" onclick="fecharModalEditar()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                <button type="button" onclick="salvarEdicao()" class="bg-eletblue hover:bg-blue-900 text-white font-bold px-6 py-2 rounded-lg shadow transition">
                    Salvar Alterações
                </button>
            </div>
        </div>
    </div>

    <template id="tpl-unidade">
        <div class="unidade-row bg-white border border-gray-200 rounded-lg p-3 flex flex-col md:flex-row gap-3 items-start md:items-center shadow-sm relative group hover:border-eletblue transition-colors">
            
            <div class="absolute top-2 right-2 md:relative md:top-0 md:right-0 md:order-last flex gap-1">
                <button type="button" onclick="abrirModalEdicao(this)" class="text-gray-300 hover:text-eletblue transition p-1" title="Editar Dados Técnicos">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                </button>
                <button type="button" onclick="removerLinha(this)" class="text-gray-300 hover:text-red-500 transition p-1" title="Excluir">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                </button>
            </div>

            <div class="flex flex-col md:w-1/4 border-b md:border-b-0 md:border-r border-gray-100 pr-2 pb-2 md:pb-0">
                <span class="text-[10px] font-bold uppercase text-gray-400 txt-classe">Residencial</span>
                <span class="text-sm font-bold text-eletblue txt-info-tec">M1 - 63A</span>
                
                <input type="hidden" class="inp-classe" name="unidades[INDEX][classe]">
                <input type="hidden" class="inp-categoria" name="unidades[INDEX][categoria]">
                <input type="hidden" class="inp-dim-id" name="unidades[INDEX][dimensionamento_id]">
                <input type="hidden" class="inp-info-tec" name="unidades[INDEX][info_tecnica]">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 w-full md:w-3/4">
                <div>
                    <input type="text" name="unidades[INDEX][placa]" class="inp-placa w-full border-gray-300 rounded text-sm p-1.5 focus:ring-eletblue border font-semibold text-gray-700" placeholder="Identificação (Ex: Casa 1)">
                </div>
                <div>
                    <input type="text" name="unidades[INDEX][numero_uc]" class="inp-uc w-full border-gray-300 rounded text-sm p-1.5 focus:ring-eletblue border" placeholder="Nº UC (Opcional)">
                </div>
                <div>
                    <input type="text" name="unidades[INDEX][observacao]" class="inp-obs w-full border-gray-300 rounded text-sm p-1.5 focus:ring-eletblue border" placeholder="Obs">
                </div>
            </div>
        </div>
    </template>

<script>
        let dadosDimensionamentoCache = [];
        let unidadeCount = 0; // Contador único para IDs

        // --- 1. LÓGICA DE API (Busca Disjuntores) ---
        async function atualizarDimensionamentoGlobal() {
            const conc = document.getElementById('concessionaria_id').value;
            const tens = document.getElementById('tensao_id').value;
            if(!conc || !tens) return;

            // Feedback visual no select
            const selectAdd = document.getElementById('add_disjuntor');
            selectAdd.innerHTML = '<option value="">Carregando padrões...</option>';

            try {
                const resp = await fetch(`<?= base_url('projeto/api/dimensionamento') ?>?concessionaria=${conc}&tensao=${tens}`);
                dadosDimensionamentoCache = await resp.json();
                filtrarDisjuntoresLocal(); // Atualiza o select de adicionar
            } catch (e) { 
                console.error("Erro API", e);
                selectAdd.innerHTML = '<option value="">Erro ao carregar</option>';
            }
        }

        function filtrarDisjuntoresLocal() {
            const cat = document.getElementById('add_categoria').value;
            const select = document.getElementById('add_disjuntor');
            select.innerHTML = '';

            if(cat === 'Calcular'){
                select.add(new Option("Cálculo Automático (Manual)", "0"));
                return;
            }

            const validos = dadosDimensionamentoCache.filter(d => d.categoria === cat);
            
            if(validos.length === 0) {
                select.add(new Option("Nenhum padrão disponível para esta tensão", ""));
            } else {
                validos.forEach(d => {
                    select.add(new Option(`${d.subcategoria} - ${d.corrente_disjuntor}A (${d.tipo_disjuntor})`, d.id));
                });
            }
        }

        // --- 2. LÓGICA DE LISTA (ADICIONAR/REMOVER) ---
        
        function adicionarNaLista() {
            const emptyState = document.getElementById('empty-state');
            
            // Pega valores do painel de adição
            const classe = document.getElementById('add_classe').value;
            const categoria = document.getElementById('add_categoria').value;
            const dimId = document.getElementById('add_disjuntor').value;
            const qtd = parseInt(document.getElementById('add_qtd').value) || 1;
            
            // Validação simples
            if(!dimId && categoria !== 'Calcular') {
                // Não faz nada se não tiver disjuntor selecionado (exceto se for calcular)
                return;
            }

            // Descobre texto técnico para exibição
            let infoTecnica = "A Calcular";
            if(dimId !== "0" && dimId !== "") {
                const dim = dadosDimensionamentoCache.find(d => d.id == dimId);
                if(dim) infoTecnica = `${dim.subcategoria} - ${dim.corrente_disjuntor}A`;
            }

            // Remove aviso de vazio se existir
            if(emptyState) emptyState.remove();

            // Loop para criar X linhas
            for(let i=0; i < qtd; i++) {
                criarLinha(classe, categoria, dimId, infoTecnica);
            }
            
            // NOTA: Não limpamos os campos de cima propositalmente, 
            // para facilitar adicionar várias medições similares.
        }

        function criarLinha(classe, categoria, dimId, infoTecnica, dadosExistentes = null) {
            const container = document.getElementById('lista-unidades');
            const template = document.getElementById('tpl-unidade');
            const clone = template.content.cloneNode(true);
            const div = clone.querySelector('.unidade-row');

            // Substitui INDEX pelo contador único
            div.innerHTML = div.innerHTML.replace(/INDEX/g, unidadeCount);

            // Preenche dados visuais (somente leitura lateral)
            div.querySelector('.txt-classe').innerText = classe;
            div.querySelector('.txt-info-tec').innerText = infoTecnica;

            // Preenche inputs hidden (dados técnicos para o backend)
            div.querySelector('.inp-classe').value = classe;
            div.querySelector('.inp-categoria').value = categoria;
            div.querySelector('.inp-dim-id').value = dimId;
            div.querySelector('.inp-info-tec').value = infoTecnica;

            // Preenche os editáveis (Placa, UC, Obs)
            if(dadosExistentes) {
                // Mapeamento inteligente para aceitar JSON antigo (identificacao) e novo (placa)
                const placaValor = dadosExistentes.placa || dadosExistentes.identificacao || "";
                const ucValor    = dadosExistentes.numero_uc || "";
                const obsValor   = dadosExistentes.observacao || "";

                div.querySelector('.inp-placa').value = placaValor;
                div.querySelector('.inp-uc').value = ucValor;
                div.querySelector('.inp-obs').value = obsValor;
            } else {
                // Sugestão automática de nome para novos itens (Ex: Residencial 1)
                const totalTipo = container.querySelectorAll('.unidade-row').length + 1;
                div.querySelector('.inp-placa').value = `${classe} ${totalTipo}`;
            }

            container.appendChild(clone);
            unidadeCount++;
        }

        function removerLinha(btn) {
            btn.closest('.unidade-row').remove();
            if(document.getElementById('lista-unidades').children.length === 0) {
                document.getElementById('lista-unidades').innerHTML = '<div id="empty-state" class="text-center py-8 text-gray-400 text-sm italic bg-gray-50 rounded-lg border border-dashed border-gray-200">Nenhuma medição adicionada ainda.</div>';
            }
        }

        // --- 3. IMPORTAÇÃO INTELIGENTE ---
        function abrirModalImportacao() { document.getElementById('modal-importacao').classList.remove('hidden'); }
        function fecharModalImportacao() { 
            document.getElementById('modal-importacao').classList.add('hidden'); 
            document.getElementById('file-upload').value = ''; 
        }

        function processarImportacao() {
            const file = document.getElementById('file-upload').files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = async function(e) {
                try {
                    const data = JSON.parse(e.target.result);
                    await preencherTudo(data);
                    fecharModalImportacao();
                    // Sucesso silencioso (sem alert)
                } catch (error) {
                    console.error(error);
                    alert("Erro ao ler o arquivo .kva. Verifique o formato.");
                }
            };
            reader.readAsText(file);
        }

        async function preencherTudo(data) {
            // 1. Campos Simples (Inputs diretos)
            const campos = ['titulo_obra', 'cliente_nome', 'tipo_obra', 'cep', 'logradouro', 'numero', 'bairro', 'cidade', 'uf', 'zona', 'complemento', 'concessionaria_id', 'tensao_id', 'tipo_ramal', 'localizacao_medidor'];
            
            campos.forEach(id => {
                if (data[id]) {
                    const el = document.getElementById(id) || document.getElementsByName(id)[0];
                    if (el) el.value = data[id];
                }
            });

            // 2. Dispara carregamento da API e AGUARDA terminar para ter o cache
            await atualizarDimensionamentoGlobal();

            // 3. Preenche a Lista Detalhada
            const container = document.getElementById('lista-unidades');
            container.innerHTML = ''; 
            unidadeCount = 0;

            // CENÁRIO A: Arquivo Novo (Já tem a lista detalhada 'unidades')
            if (data.unidades && Array.isArray(data.unidades)) {
                data.unidades.forEach(u => {
                    // Passa o objeto completo 'u' como dadosExistentes
                    criarLinha(u.classe, u.categoria, u.dimensionamento_id, u.info_tecnica, u);
                });
            } 
            // CENÁRIO B: Arquivo Antigo/Resumido (Tem 'medicoes' agrupadas)
            else if (data.medicoes && Array.isArray(data.medicoes)) {
                data.medicoes.forEach(m => {
                    // Tenta recuperar info técnica do cache
                    let infoTec = "Recuperado";
                    if(m.dimensionamento_id) {
                        const dim = dadosDimensionamentoCache.find(d => d.id == m.dimensionamento_id);
                        if(dim) infoTec = `${dim.subcategoria} - ${dim.corrente_disjuntor}A`;
                    }
                    
                    // Expande pela quantidade de repetições
                    const qtd = parseInt(m.repeticoes) || 1;
                    for(let i=0; i<qtd; i++) {
                        // Passa 'm' como dados existentes, mas note que arquivos antigos 
                        // podem não ter numero_uc/obs preenchidos.
                        criarLinha(m.classe, m.categoria, m.dimensionamento_id, infoTec, m);
                    }
                });
            }
        }

        // --- INICIALIZAÇÃO ---
        document.addEventListener('DOMContentLoaded', function() {
            // Máscara CEP e ViaCEP
            const cepInput = document.getElementById('cep');
            if(cepInput) {
                cepInput.addEventListener('input', function(e) {
                    let v = e.target.value.replace(/\D/g, '').slice(0, 8);
                    if (v.length > 5) v = v.replace(/^(\d{5})(\d)/, '$1-$2');
                    e.target.value = v;
                    if (v.replace('-', '').length === 8) buscarEndereco(v.replace('-', ''));
                });
            }
            async function buscarEndereco(cep) {
                try {
                    const r = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                    const d = await r.json();
                    if(!d.erro) {
                        document.getElementById('logradouro').value = d.logradouro;
                        document.getElementById('bairro').value = d.bairro;
                        document.getElementById('cidade').value = d.localidade;
                        document.getElementById('uf').value = d.uf;
                        document.getElementById('numero').focus();
                    }
                } catch(e){}
            }
        });

        // --- VARIÁVEL GLOBAL PARA EDIÇÃO ---
        let linhaEmEdicao = null;

        // --- LÓGICA DE LISTA ---
        // (Mantenha a função adicionarNaLista e criarLinha como estavam)
        
        function removerLinha(btn) {
            // [NOVO] Confirmação antes de excluir
            if (confirm("Tem certeza que deseja excluir esta medição?")) {
                btn.closest('.unidade-row').remove();
                if(document.getElementById('lista-unidades').children.length === 0) {
                    document.getElementById('lista-unidades').innerHTML = '<div id="empty-state" class="text-center py-8 text-gray-400 text-sm italic bg-gray-50 rounded-lg border border-dashed border-gray-200">Nenhuma medição adicionada ainda.</div>';
                }
            }
        }

        // --- LÓGICA DO MODAL DE EDIÇÃO ---

        function abrirModalEdicao(btn) {
            linhaEmEdicao = btn.closest('.unidade-row');
            
            // 1. Pega valores atuais da linha
            const classeAtual = linhaEmEdicao.querySelector('.inp-classe').value;
            const catAtual = linhaEmEdicao.querySelector('.inp-categoria').value;
            const dimIdAtual = linhaEmEdicao.querySelector('.inp-dim-id').value;

            // 2. Preenche os campos do modal
            document.getElementById('edit_classe').value = classeAtual;
            document.getElementById('edit_categoria').value = catAtual;
            
            // 3. Atualiza lista de disjuntores baseada na categoria
            filtrarDisjuntoresModal();

            // 4. Seleciona o disjuntor correto (precisa ser depois de filtrar)
            document.getElementById('edit_disjuntor').value = dimIdAtual;

            // 5. Mostra Modal
            document.getElementById('modal-editar').classList.remove('hidden');
        }

        function fecharModalEditar() {
            document.getElementById('modal-editar').classList.add('hidden');
            linhaEmEdicao = null;
        }

        function filtrarDisjuntoresModal() {
            const cat = document.getElementById('edit_categoria').value;
            const select = document.getElementById('edit_disjuntor');
            select.innerHTML = '';

            if(cat === 'Calcular'){
                select.add(new Option("Cálculo Automático", "0"));
                return;
            }

            // Usa o cache global que já temos
            const validos = dadosDimensionamentoCache.filter(d => d.categoria === cat);
            if(validos.length === 0) {
                select.add(new Option("Sem padrão disponível", ""));
            } else {
                validos.forEach(d => {
                    select.add(new Option(`${d.subcategoria} - ${d.corrente_disjuntor}A (${d.tipo_disjuntor})`, d.id));
                });
            }
        }

        function salvarEdicao() {
            if(!linhaEmEdicao) return;

            // 1. Pega novos valores do modal
            const novaClasse = document.getElementById('edit_classe').value;
            const novaCat = document.getElementById('edit_categoria').value;
            const novoDimId = document.getElementById('edit_disjuntor').value;

            // 2. Validação simples
            if(!novoDimId && novaCat !== 'Calcular') {
                alert("Selecione um disjuntor.");
                return;
            }

            // 3. Descobre o novo texto técnico
            let novaInfoTec = "A Calcular";
            if(novoDimId !== "0" && novoDimId !== "") {
                const dim = dadosDimensionamentoCache.find(d => d.id == novoDimId);
                if(dim) novaInfoTec = `${dim.subcategoria} - ${dim.corrente_disjuntor}A`;
            }

            // 4. Atualiza a LINHA (Inputs Hidden)
            linhaEmEdicao.querySelector('.inp-classe').value = novaClasse;
            linhaEmEdicao.querySelector('.inp-categoria').value = novaCat;
            linhaEmEdicao.querySelector('.inp-dim-id').value = novoDimId;
            linhaEmEdicao.querySelector('.inp-info-tec').value = novaInfoTec;

            // 5. Atualiza a LINHA (Visual)
            linhaEmEdicao.querySelector('.txt-classe').innerText = novaClasse;
            linhaEmEdicao.querySelector('.txt-info-tec').innerText = novaInfoTec;

            // 6. Fecha
            fecharModalEditar();
        }
    </script>
</body>
</html>