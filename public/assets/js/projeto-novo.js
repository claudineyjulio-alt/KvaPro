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

    const selectAdd = document.getElementById('add_disjuntor');
    if (selectAdd) selectAdd.innerHTML = '<option value="">Carregando...</option>';

    try {
        const resp = await fetch(`${API_URL_DIMENSIONAMENTO}?concessionaria=${conc}&tensao=${tens}`);
        dadosDimensionamentoCache = await resp.json();

        // Atualiza o painel de adicionar
        filtrarDisjuntoresLocalPainel();

    } catch (e) {
        console.error("Erro API", e);
        if (selectAdd) selectAdd.innerHTML = '<option value="">Erro ao carregar</option>';
    }
}

// --- VERIFICAÇÃO DE RAMAL ---
function isSubterraneo() {
    const ramalSelect = document.getElementById('tipo_ramal');
    if (!ramalSelect) return false;
    const valor = ramalSelect.value.toLowerCase();
    return valor.includes('subterrane') || valor.includes('subterrâneo');
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

    const validos = dadosDimensionamentoCache.filter(d => d.categoria == cat);
    if (validos.length === 0) {
        select.add(new Option("Nenhum padrão disponível", ""));
    } else {
        validos.forEach(d => {
            select.add(new Option(`${d.subcategoria} - ${d.corrente_disjuntor}A (${d.tipo_disjuntor})`, d.id));
        });
    }

    preencherDadosCabosPainel();
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

    if (categoria === '1') {
        // Pega a fase mais "livre" (índice 0)
        const escolhida = fasesOrdenadas[0];
        cargasFases[escolhida]++; // Aumenta o peso dela
        return escolhida;
    } else if (categoria === '2') {
        // Pega as DUAS fases mais "livres" (índice 0 e 1)
        const f1 = fasesOrdenadas[0];
        const f2 = fasesOrdenadas[1];

        cargasFases[f1]++;
        cargasFases[f2]++;

        // Retorna ordenado alfabeticamente (Ex: "AC" em vez de "CA")
        return [f1, f2].sort().join('');
    } else if (categoria === '3') {
        cargasFases['A']++;
        cargasFases['B']++;
        cargasFases['C']++;
        return 'ABC';
    }
    return '';
}

function getOpcoesFase(categoria) {
    if (categoria === '1') return ['A', 'B', 'C'];
    // Garante que todas as combinações possíveis apareçam
    if (categoria === '2') return ['AB', 'AC', 'BC', 'A', 'B'];
    if (categoria === '3') return ['ABC'];
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

    // Pega os cabos separados do painel
    const qtdFaseAdd = document.getElementById('qtd_fase').value;
    const faseAdd = document.getElementById('fase_add').value;
    const neutroAdd = document.getElementById('neutro_add').value;
    const eletrodutoAdd = document.getElementById('eletroduto_add').value;

    // PEGA OS DADOS DE IDENTIFICAÇÃO (PREFIXO E NUMERAÇÃO)
    const prefixo = document.getElementById('prefixo_add').value || '';
    let numeroAtual = parseInt(document.getElementById('numero_inicial_add').value) || 1;
    // CORREÇÃO: Adicionado || 1 para evitar falha matemática (NaN)
    const incremento = parseInt(document.getElementById('incremento_add').value) || 1;

    if (!dimId && categoria !== 'Calcular') return;

    // Prepara objeto com dados bases (Pegando o que o usuário preencheu no painel)
    let dadosBase = {
        classe: classe,
        categoria: categoria,
        dimensionamento_id: dimId,
        qtd_fase: qtdFaseAdd,
        fase: faseAdd,
        neutro: neutroAdd,
        eletroduto: eletrodutoAdd
    };

    // Se tiver um padrão selecionado, pega APENAS o que falta (Disjuntor)
    if (dimId !== "0" && dimId !== "") {
        const dim = dadosDimensionamentoCache.find(d => d.id == dimId);
        if (dim) {
            dadosBase.disjuntor = dim.corrente_disjuntor ;
            dadosBase.info_tecnica = `${dim.subcategoria} - ${dim.corrente_disjuntor}A`;
        }
    }

    if (emptyState) emptyState.remove();

    for (let i = 0; i < qtd; i++) {
        // CORREÇÃO: Cria uma cópia independente dos dados para esta volta do loop
        let dadosLinha = { ...dadosBase };

        // Calcula fase automática
        dadosLinha.fases_especificas = getProximaFase(categoria);

        // Agora a variável dadosLinha existe e pode receber a placa
        dadosLinha.placa = prefixo + numeroAtual;

        // Envia a linha clonada e processada para criação
        criarLinha(dadosLinha);

        // Prepara o número para a próxima unidade
        numeroAtual += incremento;
    }
    if (typeof showModalMessagens === 'function') {
        // Usando a crase (`) para injetar a variável ${qtd} no meio do texto
        showModalMessagens(
            'success',
            'SUCESSO!',
            `${qtd} unidade(s) adicionada(s) ao projeto com sucesso.`
        );
    }

    // Atualiza o painel visualmente com o próximo número disponível
    document.getElementById('numero_inicial_add').value = numeroAtual;
}

function atualizarNumeracaoMedidores() {
    const linhas = document.querySelectorAll('#lista-unidades .unidade-row');
    const total = linhas.length; // Pega a quantidade exata de linhas na tela

    linhas.forEach((row, index) => {
        // O index começa em 0, então somamos 1
        const spanNum = row.querySelector('.medidor-num');
        if (spanNum) spanNum.innerText = index + 1;
    });

    // --- [NOVO] ATUALIZA O INPUT HIDDEN DO TOTAL DE MEDIDORES ---
    const inputTotal = document.getElementById('input_total_medidores');
    if (inputTotal) {
        // Garante que se apagar tudo, não envie zero (ou deixe 0 se o seu sistema aceitar projeto sem medição)
        inputTotal.value = total > 0 ? total : 1;
    }
}

// function atualizarNumeracaoMedidores() {
//     const linhas = document.querySelectorAll('#lista-unidades .unidade-row');
//     linhas.forEach((row, index) => {
//         // O index começa em 0, então somamos 1
//         const spanNum = row.querySelector('.medidor-num');
//         if (spanNum) spanNum.innerText = index + 1;
//     });
// }

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
    // row.querySelector('.cls-cabo').value = dados.cabo || '';
    // NOVOS CAMPOS SEPARADOS
    row.querySelector('.cls-qtd-fase').value = dados.qtd_fase_add || '1';
    row.querySelector('.cls-fase').value = dados.fase || '';
    row.querySelector('.cls-neutro').value = dados.neutro || '';

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
    // row.querySelector('.cls-mbt').value = obterNumeroPolos(selCat.value);

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

    // const inputMbt = row.querySelector('.cls-mbt');
    // if (inputMbt) {
    //     inputMbt.value = obterNumeroPolos(novaCat);
    // }

    // 1. Atualiza lista de padrões
    popularSelectPadraoDaLinha(row, ''); // Reseta o padrão selecionado

    // 2. Atualiza lista de fases
    popularSelectFasesDaLinha(row, ''); // Reseta a fase
}

// Chamado quando muda o Padrão na linha
function aoMudarPadraoDaLinha(selectDim) {
    const row = selectDim.closest('.unidade-row');
    const dimId = selectDim.value;

    if (dimId && dimId !== '0') {
        const dim = dadosDimensionamentoCache.find(d => d.id == dimId);
        if (dim) {
            row.querySelector('.cls-qtd-fase').value = dim.qtd_cabos_fase || '1';

            // [NOVO] Lógica de Ramal
            if (isSubterraneo()) {
                row.querySelector('.cls-fase').value = dim.sub_fase || '';
                row.querySelector('.cls-neutro').value = dim.sub_neutro || '';
            } else {
                row.querySelector('.cls-fase').value = dim.aereo_fase || '';
                row.querySelector('.cls-neutro').value = dim.aereo_neutro || '';
            }

            row.querySelector('.cls-eletroduto').value = dim.eletroduto || '';
            row.querySelector('.cls-disjuntor').value = dim.corrente_disjuntor ;
            row.querySelector('.inp-info-tec').value = `${dim.subcategoria} - ${dim.corrente_disjuntor}A`;
        }
    }
}

// function aoMudarPadraoDaLinha(selectDim) {
//     const row = selectDim.closest('.unidade-row');
//     const dimId = selectDim.value;

//     // Auto-preenche os dados técnicos
//     if (dimId && dimId !== '0') {
//         const dim = dadosDimensionamentoCache.find(d => d.id == dimId);
//         if (dim) {
//             // row.querySelector('.cls-cabo').value = dim.cabo || '';
//             // NOVOS CAMPOS SEPARADOS
//             row.querySelector('.cls-qtd-fase').value = dim.qtd_cabos_fase || '1';
//             row.querySelector('.cls-fase').value = dim.secao_fase || '';
//             row.querySelector('.cls-neutro').value = dim.secao_neutro || '';

//             row.querySelector('.cls-eletroduto').value = dim.eletroduto || '';
//             row.querySelector('.cls-disjuntor').value = dim.corrente_disjuntor + "A";
//             row.querySelector('.inp-info-tec').value = `${dim.subcategoria} - ${dim.corrente_disjuntor}A`;
//         }
//     }
// }

// --- POPULADORES DE SELECT (INDIVIDUAL POR LINHA) ---

function popularSelectPadraoDaLinha(row, valorSelecionado) {
    const cat = row.querySelector('.cls-categoria').value;
    const select = row.querySelector('.cls-dim-id');
    select.innerHTML = '';

    if (cat === 'Calcular') {
        select.add(new Option("Cálculo Automático", "0"));
    } else {
        const validos = dadosDimensionamentoCache.filter(d => d.categoria == cat);
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

// // Converte a letra no número de polos
// function obterNumeroPolos(categoria) {
//     if (categoria === '1') return '1';
//     if (categoria === '2') return '2';
//     if (categoria === '3') return '3';
//     return '1'; // Padrão
// }

// --- PREENCHE OS CAMPOS DE CABO/ELETRODUTO NO PAINEL DE ADICIONAR ---
function preencherDadosCabosPainel() {
    const dimId = document.getElementById('add_disjuntor').value;

    const inputQtd = document.getElementById('qtd_fase_add');
    const inputFase = document.getElementById('fase_add');
    const inputNeutro = document.getElementById('neutro_add');
    const inputEletroduto = document.getElementById('eletroduto_add');

    if (!dimId || dimId === "0") {
        inputQtd.value = '1';
        inputFase.value = '';
        inputNeutro.value = '';
        inputEletroduto.value = '';
        return;
    }

    const dim = dadosDimensionamentoCache.find(d => d.id == dimId);

    if (dim) {
        inputQtd.value = dim.qtd_cabos_fase || '1';

        // [NOVO] Lógica de Ramal: Escolhe o cabo certo!
        if (isSubterraneo()) {
            inputFase.value = dim.sub_fase || '';
            inputNeutro.value = dim.sub_neutro || '';
        } else {
            inputFase.value = dim.aereo_fase || '';
            inputNeutro.value = dim.aereo_neutro || '';
        }

        inputEletroduto.value = dim.eletroduto || '';
    }
}

// function preencherDadosCabosPainel() {
//     const dimId = document.getElementById('add_disjuntor').value;

//     // Pega os inputs
//     const inputQtd = document.getElementById('qtd_fase_add'); // Notei que no seu HTML o id está qtd_fase_add (sem _add)
//     const inputFase = document.getElementById('fase_add');
//     const inputNeutro = document.getElementById('neutro_add');
//     const inputEletroduto = document.getElementById('eletroduto_add');

//     // Se escolheu "A Calcular" (0) ou vazio, limpa os campos
//     if (!dimId || dimId === "0") {
//         inputQtd.value = '1';
//         inputFase.value = '';
//         inputNeutro.value = '';
//         inputEletroduto.value = '';
//         return;
//     }

//     // Procura no cache o dimensionamento escolhido
//     const dim = dadosDimensionamentoCache.find(d => d.id == dimId);

//     if (dim) {
//         // Preenche os inputs com os dados do banco (ajuste os nomes das propriedades se vierem diferentes na API)
//         inputQtd.value = dim.qtd_cabos_fase || '1';
//         inputFase.value = dim.secao_fase || '';
//         inputNeutro.value = dim.secao_neutro || '';

//         // Mantive eletroduto assumindo que a coluna continua se chamando assim
//         inputEletroduto.value = dim.eletroduto || '';
//     }
// }

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

    // const campos = [
    //     'titulo_obra', 'cliente_nome', 'tipo_obra', 'cep', 'logradouro', 'numero', 'bairro', 'cidade', 'uf', 'zona',
    //     'concessionaria_id', 'tensao_id', 'tipo_ramal', 'localizacao_medidor',
    //     'entrada_cabo', 'entrada_eletroduto', 'entrada_disjuntor', 'numero_fases',
    //     'dps_tensao', 'dps_ka', 'dps_cabo',
    //     'terra_cabo', 'terra_tubo', 'terra_hastes'
    // ];
    // campos.forEach(id => {
    //     if (data[id]) {
    //         const el = document.getElementById(id) || document.getElementsByName(id)[0];
    //         if (el) el.value = data[id];
    //     }
    // });

    /**
 * Popula o formulário dinamicamente baseando-se nas chaves do objeto JSON
 */
    function popularFormulario(data) {
        if (!data) return;

        // Varre todas as chaves do objeto de dados (ex: 'cliente_nome', 'entrada_fase', etc)
        Object.keys(data).forEach(key => {
            const valor = data[key];

            // 1. Caso Especial: Lista de Unidades (Array)
            // Se a chave for 'unidades' e for um array, chamamos a função que recria a tabela
            if (key === 'unidades' && Array.isArray(valor)) {
                recriarUnidades(valor);
                return; // Pula para a próxima chave
            }

            // 2. Tenta achar o elemento pelo ID ou pelo NAME
            // (Prioriza ID, se não achar, busca pelo name)
            let elemento = document.getElementById(key) || document.querySelector(`[name="${key}"]`);

            // Se não achou elemento simples, verifica se é um grupo de Radio Buttons
            if (!elemento) {
                const radios = document.querySelectorAll(`input[name="${key}"][type="radio"]`);
                if (radios.length > 0) {
                    radios.forEach(radio => {
                        // Marca o radio que tiver o mesmo valor do dado
                        if (radio.value == valor) {
                            radio.checked = true;
                        }
                    });
                }
                return; // Já tratamos radio, pula pro próximo
            }

            // 3. Se achou o elemento, popula de acordo com o tipo
            if (elemento) {
                const tipo = elemento.type || elemento.tagName.toLowerCase();

                if (tipo === 'checkbox') {
                    // Para checkbox único (ex: "aceito termos")
                    elemento.checked = (valor == true || valor == "1" || valor == "on");
                } else {
                    // Para inputs normais (text, number, select-one, hidden, etc)
                    elemento.value = valor;
                }
            }
        });
    }

    // --- Função Auxiliar para recriar as linhas da tabela de unidades ---
    function recriarUnidades(listaUnidades) {
        // Verifique se você tem uma função que adiciona linhas na tabela.
        // O código abaixo é um exemplo genérico. Adapte para usar sua função 'adicionarUnidade()'.

        const tbody = document.querySelector('#tabela-unidades tbody');
        if (!tbody) return;

        tbody.innerHTML = ''; // Limpa a tabela atual para não duplicar

        listaUnidades.forEach(unidade => {
            // Aqui você deve chamar a função que você já usa para criar uma nova linha (TR)
            // Exemplo hipotético:
            // adicionarNovaLinha(unidade); 

            // OU, se você não tem uma função separada, seria algo assim:
            // let tr = document.createElement('tr');
            // tr.innerHTML = `<td><input name="placa[]" value="${unidade.placa}"></td>...`;
            // tbody.appendChild(tr);

            // DICA: Se o seu formulário usa name="unidades[x][placa]", 
            // você precisará de uma lógica específica aqui para recriar o índice correto.
        });
    }

    popularFormulario(data);

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
                dadosLinha.qtd_fase = dim.qtd_cabos_fase || '1';

                // [NOVO] Lógica de Ramal na Importação
                // Avalia se o JSON salvo diz que é subterrâneo
                const isSub = data.tipo_ramal && (data.tipo_ramal.toLowerCase().includes('subterrane') || data.tipo_ramal.toLowerCase().includes('subterrâneo'));

                if (isSub) {
                    dadosLinha.fase = dim.sub_fase || '';
                    dadosLinha.neutro = dim.sub_neutro || '';
                } else {
                    dadosLinha.fase = dim.aereo_fase || '';
                    dadosLinha.neutro = dim.aereo_neutro || '';
                }

                dadosLinha.eletroduto = dim.eletroduto;
                dadosLinha.disjuntor = dim.corrente_disjuntor ;
                dadosLinha.info_tecnica = `${dim.subcategoria} - ${dim.corrente_disjuntor}A`;
            }
            // if (dim) {
            //     // Monta o objeto com os dados do cache em vez de tentar alterar o HTML aqui
            //     dadosLinha.qtd_fase = dim.qtd_cabos_fase || '1';
            //     dadosLinha.fase = dim.secao_fase || '';
            //     dadosLinha.neutro = dim.secao_neutro || '';
            //     dadosLinha.eletroduto = dim.eletroduto;
            //     dadosLinha.disjuntor = dim.corrente_disjuntor + "A";
            //     dadosLinha.info_tecnica = `${dim.subcategoria} - ${dim.corrente_disjuntor}A`;
            // }
            // if (dim) {
            //     // dadosLinha.cabo = dim.cabo;
            //     // NOVOS CAMPOS SEPARADOS
            //     row.querySelector('.cls-qtd-fase').value = dados.qtd_fase || '1';
            //     row.querySelector('.cls-fase').value = dados.fase || '';
            //     row.querySelector('.cls-neutro').value = dados.neutro || '';
            //     dadosLinha.eletroduto = dim.eletroduto;
            //     dadosLinha.disjuntor = dim.corrente_disjuntor + "A";
            //     dadosLinha.info_tecnica = `${dim.subcategoria} - ${dim.corrente_disjuntor}A`;
            // }
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

        // Usa a nova função centralizada
        if (isSubterraneo()) {
            travessiaDiv.style.display = 'none';
            if (travessiaInput) travessiaInput.value = '';
        } else {
            travessiaDiv.style.display = 'block';
        }

        // 1. Atualiza os cabos do painel "Adicionar"
        preencherDadosCabosPainel();

        // 2. Atualiza os cabos de TODAS as unidades que já estão na tabela embaixo
        atualizarCabosDasLinhas();
    }

    // [NOVA FUNÇÃO] Varre a tabela e manda atualizar os cabos de quem já foi criado
    function atualizarCabosDasLinhas() {
        const selectsDim = document.querySelectorAll('.unidade-row .cls-dim-id');
        selectsDim.forEach(select => {
            // Dispara a mesma função que roda quando o usuário escolhe um padrão manualmente
            aoMudarPadraoDaLinha(select);
        });
    }
    // // Função que verifica e esconde/mostra
    // function toggleTravessia() {
    //     const ramalSelect = document.getElementById('tipo_ramal');
    //     const travessiaDiv = document.getElementById('div_travessia');
    //     const travessiaInput = document.getElementById('travessia');

    //     if (!ramalSelect || !travessiaDiv) return;

    //     const valorRamal = ramalSelect.value.toLowerCase();

    //     // Lógica para esconder/mostrar sem Tailwind
    //     if (valorRamal.includes('subterrane') || valorRamal.includes('subterrâneo')) {
    //         travessiaDiv.style.display = 'none'; // Mudou de .classList.add('hidden')
    //         if (travessiaInput) travessiaInput.value = '';
    //     } else {
    //         travessiaDiv.style.display = 'block'; // Mudou de .classList.remove('hidden')
    //     }
    //     preencherDadosCabosPainel();
    // }

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
