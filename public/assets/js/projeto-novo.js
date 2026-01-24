// public/assets/js/projeto-novo.js

let dadosDimensionamentoCache = [];
let unidadeCount = 0;

// Contadores para distribuição automática
let contadorFasesM = 0;
let contadorFasesB = 0;

// --- 1. LÓGICA DE API ---
async function atualizarDimensionamentoGlobal() {
    const conc = document.getElementById('concessionaria_id').value;
    const tens = document.getElementById('tensao_id').value;
    if (!conc || !tens) return;

    // Feedback no painel de adicionar lá em cima
    const selectAdd = document.getElementById('add_disjuntor');
    if (selectAdd) selectAdd.innerHTML = '<option value="">Carregando padrões...</option>';

    try {
        const resp = await fetch(`${API_URL_DIMENSIONAMENTO}?concessionaria=${conc}&tensao=${tens}`);
        dadosDimensionamentoCache = await resp.json();

        // Atualiza o painel de adicionar
        filtrarDisjuntoresLocalPainel();

        // [NOVO] Atualiza as linhas existentes caso mude a concessionária/tensão (opcional, mas recomendado)
        // atualizarTodasAsLinhas(); 
    } catch (e) {
        console.error("Erro API", e);
        if (selectAdd) selectAdd.innerHTML = '<option value="">Erro ao carregar</option>';
    }
}

// Filtra o select "lá de cima" (Painel Adicionar)
function filtrarDisjuntoresLocalPainel() {
    const cat = document.getElementById('add_categoria').value;
    const select = document.getElementById('add_disjuntor');
    select.innerHTML = '';

    if (cat === 'Calcular') {
        select.add(new Option("Cálculo Automático (Manual)", "0"));
        return;
    }

    const validos = dadosDimensionamentoCache.filter(d => d.categoria === cat);
    if (validos.length === 0) {
        select.add(new Option("Nenhum padrão disponível", ""));
    } else {
        validos.forEach(d => {
            select.add(new Option(`${d.subcategoria} - ${d.corrente_disjuntor}A (${d.tipo_disjuntor})`, d.id));
        });
    }
}

// --- FUNÇÕES AUXILIARES DE FASE ---
// [NOVO] Contador Global de Cargas (Quantas vezes cada fase foi usada)
let cargasFases = {
    'A': 0,
    'B': 0,
    'C': 0
};

// --- FUNÇÃO INTELIGENTE DE DISTRIBUIÇÃO ---
function getProximaFase(categoria) {
    // Cria lista das fases ordenadas da MENOS usada para a MAIS usada
    // Em caso de empate, usa ordem alfabética
    const fasesOrdenadas = Object.keys(cargasFases).sort((a, b) => {
        return cargasFases[a] - cargasFases[b] || a.localeCompare(b);
    });

    if (categoria === 'M') {
        // Pega a fase mais "livre" (índice 0)
        const escolhida = fasesOrdenadas[0];
        cargasFases[escolhida]++; // Aumenta o peso dela
        return escolhida;
    } else if (categoria === 'B') {
        // Pega as DUAS fases mais "livres" (índice 0 e 1)
        const f1 = fasesOrdenadas[0];
        const f2 = fasesOrdenadas[1];

        cargasFases[f1]++;
        cargasFases[f2]++;

        // Retorna ordenado alfabeticamente (Ex: "AC" em vez de "CA")
        return [f1, f2].sort().join('');
    } else if (categoria === 'T') {
        cargasFases['A']++;
        cargasFases['B']++;
        cargasFases['C']++;
        return 'ABC';
    }
    return '';
}

function getOpcoesFase(categoria) {
    if (categoria === 'M') return ['A', 'B', 'C'];
    // Garante que todas as combinações possíveis apareçam
    if (categoria === 'B') return ['AB', 'AC', 'BC', 'A', 'B'];
    if (categoria === 'T') return ['ABC'];
    return [''];
}
// --- 2. LÓGICA DE LISTA (CRIAÇÃO) ---

function adicionarNaLista() {
    const emptyState = document.getElementById('empty-state');

    // Pega os dados INICIAIS do painel de cima
    const classe = document.getElementById('add_classe').value;
    const categoria = document.getElementById('add_categoria').value;
    const dimId = document.getElementById('add_disjuntor').value;
    const qtd = parseInt(document.getElementById('add_qtd').value) || 1;

    if (!dimId && categoria !== 'Calcular') return;

    // Prepara objeto com dados iniciais
    let dadosIniciais = {
        classe: classe,
        categoria: categoria,
        dimensionamento_id: dimId
    };

    // Se tiver um padrão selecionado, pega os detalhes técnicos
    if (dimId !== "0" && dimId !== "") {
        const dim = dadosDimensionamentoCache.find(d => d.id == dimId);
        if (dim) {
            dadosIniciais.cabo = dim.cabo;
            dadosIniciais.eletroduto = dim.eletroduto;
            dadosIniciais.disjuntor = dim.corrente_disjuntor + "A";
            dadosIniciais.info_tecnica = `${dim.subcategoria} - ${dim.corrente_disjuntor}A`;
        }
    }

    if (emptyState) emptyState.remove();

    for (let i = 0; i < qtd; i++) {
        // Calcula fase automática
        dadosIniciais.fases_especificas = getProximaFase(categoria);
        criarLinha(dadosIniciais);
    }
}


function atualizarNumeracaoMedidores() {
    const linhas = document.querySelectorAll('#lista-unidades .unidade-row');
    linhas.forEach((row, index) => {
        // O index começa em 0, então somamos 1
        const spanNum = row.querySelector('.medidor-num');
        if(spanNum) spanNum.innerText = index + 1;
    });
}

// [PRINCIPAL] Cria a linha já editável
function criarLinha(dados) {
    const container = document.getElementById('lista-unidades');
    const template = document.getElementById('tpl-unidade');
    const clone = template.content.cloneNode(true);
    const row = clone.querySelector('.unidade-row');

    // ID único
    row.innerHTML = row.innerHTML.replace(/INDEX/g, unidadeCount);

    // 1. Preenche Inputs de Texto Simples
    row.querySelector('.cls-classe').value = dados.classe || 'Residencial';
    row.querySelector('.cls-cabo').value = dados.cabo || '';
    row.querySelector('.cls-eletroduto').value = dados.eletroduto || '';
    row.querySelector('.cls-disjuntor').value = dados.disjuntor || '';
    row.querySelector('.inp-info-tec').value = dados.info_tecnica || ''; // Mantido hidden legado

    const placa = dados.placa || dados.identificacao || '';
    if (placa) row.querySelector('.cls-placa').value = placa;
    else {
        const num = container.querySelectorAll('.unidade-row').length + 1;
        row.querySelector('.cls-placa').value = `${dados.classe} ${num}`;
    }

    row.querySelector('.cls-uc').value = dados.numero_uc || '';
    row.querySelector('.cls-obs').value = dados.observacao || '';

    // 2. Configura Select de Categoria
    const selCat = row.querySelector('.cls-categoria');
    selCat.value = dados.categoria;

    // 3. Popula Select de Padrão/Dimensionamento (Baseado na categoria)
    popularSelectPadraoDaLinha(row, dados.dimensionamento_id);

    // 4. Popula Select de Fases (Baseado na categoria)
    popularSelectFasesDaLinha(row, dados.fases_especificas);

    container.appendChild(clone);
    unidadeCount++;

    atualizarNumeracaoMedidores();

}

// --- FUNÇÕES DE EVENTO DA LINHA (AUTO-COMPLETE) ---

// Chamado quando muda a Categoria na linha
function aoMudarCategoriaDaLinha(selectCat) {
    const row = selectCat.closest('.unidade-row');
    const novaCat = selectCat.value;

    // 1. Atualiza lista de padrões
    popularSelectPadraoDaLinha(row, ''); // Reseta o padrão selecionado

    // 2. Atualiza lista de fases
    popularSelectFasesDaLinha(row, ''); // Reseta a fase
}

// Chamado quando muda o Padrão na linha
function aoMudarPadraoDaLinha(selectDim) {
    const row = selectDim.closest('.unidade-row');
    const dimId = selectDim.value;

    // Auto-preenche os dados técnicos
    if (dimId && dimId !== '0') {
        const dim = dadosDimensionamentoCache.find(d => d.id == dimId);
        if (dim) {
            row.querySelector('.cls-cabo').value = dim.cabo || '';
            row.querySelector('.cls-eletroduto').value = dim.eletroduto || '';
            row.querySelector('.cls-disjuntor').value = dim.corrente_disjuntor + "A";
            row.querySelector('.inp-info-tec').value = `${dim.subcategoria} - ${dim.corrente_disjuntor}A`;
        }
    }
}

// --- POPULADORES DE SELECT (INDIVIDUAL POR LINHA) ---

function popularSelectPadraoDaLinha(row, valorSelecionado) {
    const cat = row.querySelector('.cls-categoria').value;
    const select = row.querySelector('.cls-dim-id');
    select.innerHTML = '';

    if (cat === 'Calcular') {
        select.add(new Option("Cálculo Automático", "0"));
    } else {
        const validos = dadosDimensionamentoCache.filter(d => d.categoria === cat);
        if (validos.length === 0) {
            select.add(new Option("Sem padrão", ""));
        } else {
            validos.forEach(d => {
                const opt = new Option(`${d.subcategoria} - ${d.corrente_disjuntor}A`, d.id);
                if (d.id == valorSelecionado) opt.selected = true;
                select.add(opt);
            });
        }
    }
    // Se mudou para uma categoria que não tem o ID antigo, força o valor para o primeiro ou vazio
    if (select.value !== valorSelecionado && valorSelecionado) {
        // Opcional: Avisar usuário ou limpar campos
    }
}

function popularSelectFasesDaLinha(row, valorSelecionado) {
    const cat = row.querySelector('.cls-categoria').value;
    const select = row.querySelector('.cls-fases');
    select.innerHTML = '';

    const opcoes = getOpcoesFase(cat);
    opcoes.forEach(op => {
        const opt = new Option(op, op);
        if (op === valorSelecionado) opt.selected = true;
        select.add(opt);
    });

    // Se não tiver valor selecionado (ex: nova linha manual), pega o automático (se estiver criando)
    // Mas como aqui já estamos na linha, ideal é selecionar o primeiro ou manter vazio
}

// --- REMOVER / REDISTRIBUIR ---
function removerLinha(btn) {
    if (confirm("Tem certeza que deseja excluir esta medição?")) {
        btn.closest('.unidade-row').remove();

        const lista = document.getElementById('lista-unidades');
        if (lista.children.length === 0) {
            lista.innerHTML = '<div id="empty-state" class="text-center py-8 text-gray-400 text-sm italic bg-gray-50 rounded-lg border border-dashed border-gray-200">Nenhuma medição adicionada ainda.</div>';
        } else {

            atualizarNumeracaoMedidores();

            setTimeout(() => {
                if (confirm("Deseja redistribuir as fases para rebalancear (A, B, C...)?")) {
                    redistribuirFasesDasUnidades();
                }
            }, 100);
        }
    }
}

function redistribuirFasesDasUnidades() {
    // 1. Zera os contadores globais
    cargasFases = {
        'A': 0,
        'B': 0,
        'C': 0
    };

    const linhas = document.querySelectorAll('.unidade-row');
    linhas.forEach(row => {
        const cat = row.querySelector('.cls-categoria').value;
        // Só recalcula se não for 'Calcular' e se não tiver fase fixa manual (opcional, aqui vou recalcular tudo)
        if (cat !== 'Calcular') {
            const novaFase = getProximaFase(cat);

            // Atualiza o select visível
            const selFase = row.querySelector('.cls-fases');
            // Precisamos garantir que a opção existe no select antes de selecionar
            // (Geralmente já existe, mas se mudou de tipo de fase pode dar erro visual, 
            //  mas o valor interno será salvo corretamente)
            selFase.value = novaFase;
        }
    });
}

// --- IMPORTAÇÃO ---
// (Funções abrirModalImportacao, fecharModalImportacao, processarImportacao iguais)
// Mantendo a lógica apenas atualizando o preencherTudo

async function preencherTudo(data) {
    // Preenche campos gerais
    const container = document.getElementById('lista-unidades');
    container.innerHTML = '';
    unidadeCount = 0;

    // [RESET NOVO] Zera as cargas antes de começar a importar
    cargasFases = {
        'A': 0,
        'B': 0,
        'C': 0
    };

    const campos = [
        'titulo_obra', 'cliente_nome', 'tipo_obra', 'cep', 'logradouro', 'numero', 'bairro', 'cidade', 'uf', 'zona',
        'concessionaria_id', 'tensao_id', 'tipo_ramal', 'localizacao_medidor',
        'entrada_cabo', 'entrada_eletroduto', 'entrada_disjuntor', 'numero_fases',
        'dps_tensao', 'dps_ka', 'dps_cabo',
        'terra_cabo', 'terra_tubo', 'terra_hastes'
    ];
    campos.forEach(id => {
        if (data[id]) {
            const el = document.getElementById(id) || document.getElementsByName(id)[0];
            if (el) el.value = data[id];
        }
    });

    await atualizarDimensionamentoGlobal();

    container.innerHTML = '';
    unidadeCount = 0;
    contadorFasesM = 0;
    contadorFasesB = 0;

    // Função unificada de dados
    let lista = [];
    if (data.unidades && Array.isArray(data.unidades)) lista = data.unidades;
    else if (data.medicoes && Array.isArray(data.medicoes)) lista = data.medicoes;

    lista.forEach(item => {
        // Normaliza dados legados vs novos
        let dadosLinha = {
            ...item
        };

        // Se for legado e tiver repetições
        const qtd = parseInt(item.repeticoes) || 1;

        // Tenta recuperar dados técnicos do cache se faltar
        if (item.dimensionamento_id && (!item.cabo || !item.eletroduto)) {
            const dim = dadosDimensionamentoCache.find(d => d.id == item.dimensionamento_id);
            if (dim) {
                dadosLinha.cabo = dim.cabo;
                dadosLinha.eletroduto = dim.eletroduto;
                dadosLinha.disjuntor = dim.corrente_disjuntor + "A";
                dadosLinha.info_tecnica = `${dim.subcategoria} - ${dim.corrente_disjuntor}A`;
            }
        }

        for (let i = 0; i < qtd; i++) {
            // Se não tiver fase salva, calcula
            if (!dadosLinha.fases_especificas) {
                dadosLinha.fases_especificas = getProximaFase(dadosLinha.categoria || item.categoria);
            }
            criarLinha(dadosLinha);
        }
    });
}

// Inicialização
document.addEventListener('DOMContentLoaded', function () {
    // ... (ViaCEP igual) ...
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('input', function (e) {
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
            if (!d.erro) {
                document.getElementById('logradouro').value = d.logradouro;
                document.getElementById('bairro').value = d.bairro;
                document.getElementById('cidade').value = d.localidade;
                document.getElementById('uf').value = d.uf;
                document.getElementById('numero').focus();
            }
        } catch (e) { }
    }

    // 1. Busca o elemento pelo ID
    const scriptElement = document.getElementById('dados-recuperados');

    // 2. Lê o conteúdo de texto e converte para Objeto JS
    try {
        const dadosRecuperados = JSON.parse(scriptElement.textContent);

        // 3. Se houver dados (não for null), executa a função
        if (dadosRecuperados) {
            console.log("Dados recuperados via JSON tag:", dadosRecuperados);

            // O setTimeout as vezes é necessário se o preencherTudo depender de elementos 
            // que são criados dinamicamente ou requisições AJAX (como o cache do dimensionamento)
            setTimeout(() => {
                preencherTudo(dadosRecuperados);
            }, 500);
        }
    } catch (error) {
        console.error("Erro ao ler JSON recuperado:", error);
    }


    // Pega os elementos
    const ramalSelect = document.getElementById('tipo_ramal');
    const travessiaDiv = document.getElementById('div_travessia');
    const travessiaInput = document.getElementById('travessia');

    // Função que verifica e esconde/mostra
    function toggleTravessia() {
        const ramalSelect = document.getElementById('tipo_ramal');
        const travessiaDiv = document.getElementById('div_travessia');
        const travessiaInput = document.getElementById('travessia');

        if (!ramalSelect || !travessiaDiv) return;

        const valorRamal = ramalSelect.value.toLowerCase();

        // Lógica para esconder/mostrar sem Tailwind
        if (valorRamal.includes('subterrane') || valorRamal.includes('subterrâneo')) {
            travessiaDiv.style.display = 'none'; // Mudou de .classList.add('hidden')
            if (travessiaInput) travessiaInput.value = '';
        } else {
            travessiaDiv.style.display = 'block'; // Mudou de .classList.remove('hidden')
        }
    }

    // Se o campo existir, adiciona o evento
    if (ramalSelect) {
        ramalSelect.addEventListener('change', toggleTravessia);
        // Roda uma vez ao carregar a página para garantir o estado correto
        toggleTravessia();
    } else {
        console.warn('Campo #tipo_ramal não encontrado! Verifique o ID no formulário.');
    }


});

// Funções Importação Modal
function abrirModalImportacao() {
    document.getElementById('modal-importacao').classList.remove('hidden');
}

function fecharModalImportacao() {
    document.getElementById('modal-importacao').classList.add('hidden');
    document.getElementById('file-upload').value = '';
}

function processarImportacao() {
    const file = document.getElementById('file-upload').files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = async function (e) {
        try {
            const data = JSON.parse(e.target.result);
            await preencherTudo(data);
            fecharModalImportacao();
        } catch (error) {
            console.error(error);
            alert("Erro arquivo.");
        }
    };
    reader.readAsText(file);
}
