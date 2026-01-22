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
                    <span class="bg-eletblue text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">1</span>
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
                    <span class="bg-eletblue text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">2</span>
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

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <span class="bg-eletblue text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">3</span>
                    <h2 class="font-semibold text-gray-700">Geral</h2>
                </div>
                
                <div class="p-6">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3 border-b border-gray-100 pb-1">Padrão de Entrada</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Cabo de Entrada *</label>
                            <input type="text" name="entrada_cabo" id="entrada_cabo" placeholder="Ex: 3#35(35)mm²" required class="w-full border-gray-300 rounded text-sm p-2 border focus:ring-eletblue focus:border-eletblue">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Eletroduto Entrada *</label>
                            <input type="text" name="entrada_eletroduto" id="entrada_eletroduto" placeholder='Ex: Ø 2"' required class="w-full border-gray-300 rounded text-sm p-2 border focus:ring-eletblue focus:border-eletblue">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Disjuntor Geral *</label>
                            <input type="text" name="entrada_disjuntor" id="entrada_disjuntor" placeholder="Ex: 100A" required class="w-full border-gray-300 rounded text-sm p-2 border focus:ring-eletblue focus:border-eletblue">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Fases (Geral) *</label>
                            <select name="numero_fases" id="numero_fases" class="w-full border-gray-300 rounded text-sm p-2 bg-white border focus:ring-eletblue focus:border-eletblue">
                                <option value="3">Trifásico (3 Fases)</option>
                                <option value="2">Bifásico (2 Fases)</option>
                                <option value="1">Monofásico (1 Fase)</option>
                            </select>
                        </div>
                    </div>

                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3 border-b border-gray-100 pb-1">Proteção (DPS)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Tensão DPS</label>
                            <input type="text" name="dps_tensao" id="dps_tensao" value="275V" placeholder="Ex: 275V" class="w-full border-gray-300 rounded text-sm p-2 border focus:ring-eletblue focus:border-eletblue">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Capacidade (kA)</label>
                            <input type="text" name="dps_ka" id="dps_ka" value="20kA" placeholder="Ex: 20kA" class="w-full border-gray-300 rounded text-sm p-2 border focus:ring-eletblue focus:border-eletblue">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Cabo DPS</label>
                            <input type="text" name="dps_cabo" id="dps_cabo" value="#10mm²" placeholder="Ex: #10mm²" class="w-full border-gray-300 rounded text-sm p-2 border focus:ring-eletblue focus:border-eletblue">
                        </div>
                    </div>

                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3 border-b border-gray-100 pb-1">Aterramento</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Cabo Terra</label>
                            <input type="text" name="terra_cabo" id="terra_cabo" value="Cobre Nú #35mm²" placeholder="Ex: Cobre Nú #35mm²" class="w-full border-gray-300 rounded text-sm p-2 border focus:ring-eletblue focus:border-eletblue">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Eletroduto Terra</label>
                            <input type="text" name="terra_tubo" id="terra_tubo" value='Ø 3/4"' placeholder='Ex: Ø 3/4"' class="w-full border-gray-300 rounded text-sm p-2 border focus:ring-eletblue focus:border-eletblue">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Hastes</label>
                            <input type="text" name="terra_hastes" id="terra_hastes" value="3 Hastes Alta Camada" placeholder="Ex: 3 Hastes 2.40m" class="w-full border-gray-300 rounded text-sm p-2 border focus:ring-eletblue focus:border-eletblue">
                        </div>
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


    <template id="tpl-unidade">
        <div class="unidade-row bg-white border border-gray-200 rounded-lg p-4 mb-3 shadow-sm relative group hover:border-eletblue transition-all">
            
            <button type="button" onclick="removerLinha(this)" class="absolute top-3 right-3 text-gray-300 hover:text-red-500 transition p-1" title="Excluir">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
            </button>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mb-3 pr-8">
                
                <div class="md:col-span-3">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Classe</label>
                    <select name="unidades[INDEX][classe]" class="cls-classe w-full border-gray-300 rounded text-sm p-1.5 focus:ring-eletblue bg-gray-50 focus:bg-white border">
                        <option value="Residencial">Residencial</option>
                        <option value="Comercial">Comercial</option>
                        <option value="Industrial">Industrial</option>
                        <option value="B. Incendio">Bomba Incêndio</option>
                        <option value="Condominio">Condomínio</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Categoria</label>
                    <select name="unidades[INDEX][categoria]" class="cls-categoria w-full border-gray-300 rounded text-sm p-1.5 focus:ring-eletblue bg-gray-50 focus:bg-white border" onchange="aoMudarCategoriaDaLinha(this)">
                        <option value="M">Monofásica</option>
                        <option value="B">Bifásica</option>
                        <option value="T">Trifásica</option>
                        <option value="Calcular">A Calcular</option>
                    </select>
                </div>

                <div class="md:col-span-5">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Padrão Concessionária</label>
                    <select name="unidades[INDEX][dimensionamento_id]" class="cls-dim-id w-full border-gray-300 rounded text-sm p-1.5 focus:ring-eletblue bg-gray-50 focus:bg-white border" onchange="aoMudarPadraoDaLinha(this)">
                        </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Fases</label>
                    <select name="unidades[INDEX][fases_especificas]" class="cls-fases w-full border-gray-300 rounded text-sm p-1.5 focus:ring-eletblue bg-gray-50 focus:bg-white border">
                        </select>
                </div>
            </div>

            <div class="border-t border-gray-100 my-2"></div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Cabo Ramal</label>
                    <input type="text" name="unidades[INDEX][cabo]" class="cls-cabo w-full border-gray-300 rounded text-sm p-1.5 border focus:ring-eletblue text-gray-600" placeholder="Ex: 2#10(10)mm²">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Eletroduto</label>
                    <input type="text" name="unidades[INDEX][eletroduto]" class="cls-eletroduto w-full border-gray-300 rounded text-sm p-1.5 border focus:ring-eletblue text-gray-600" placeholder='Ex: Ø 1"'>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Disjuntor (Texto)</label>
                    <input type="text" name="unidades[INDEX][disjuntor]" class="cls-disjuntor w-full border-gray-300 rounded text-sm p-1.5 border focus:ring-eletblue text-gray-600" placeholder="Ex: 50A">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <input type="text" name="unidades[INDEX][placa]" class="cls-placa w-full border-gray-300 rounded text-sm p-1.5 focus:ring-eletblue border font-bold text-gray-800" placeholder="Identificação (Ex: Casa 1)">
                </div>
                <div>
                    <input type="text" name="unidades[INDEX][numero_uc]" class="cls-uc w-full border-gray-300 rounded text-sm p-1.5 focus:ring-eletblue border" placeholder="Nº UC (Opcional)">
                </div>
                <div>
                    <input type="text" name="unidades[INDEX][observacao]" class="cls-obs w-full border-gray-300 rounded text-sm p-1.5 focus:ring-eletblue border" placeholder="Observações">
                </div>
            </div>

            <input type="hidden" class="inp-info-tec" name="unidades[INDEX][info_tecnica]">
        </div>
    </template>

<script>
    let dadosDimensionamentoCache = [];
    let unidadeCount = 0;

    // Contadores para distribuição automática
    let contadorFasesM = 0; 
    let contadorFasesB = 0;

    // --- 1. LÓGICA DE API ---
    async function atualizarDimensionamentoGlobal() {
        const conc = document.getElementById('concessionaria_id').value;
        const tens = document.getElementById('tensao_id').value;
        if(!conc || !tens) return;

        // Feedback no painel de adicionar lá em cima
        const selectAdd = document.getElementById('add_disjuntor');
        if(selectAdd) selectAdd.innerHTML = '<option value="">Carregando padrões...</option>';

        try {
            const resp = await fetch(`<?= base_url('projeto/api/dimensionamento') ?>?concessionaria=${conc}&tensao=${tens}`);
            dadosDimensionamentoCache = await resp.json();
            
            // Atualiza o painel de adicionar
            filtrarDisjuntoresLocalPainel(); 
            
            // [NOVO] Atualiza as linhas existentes caso mude a concessionária/tensão (opcional, mas recomendado)
            // atualizarTodasAsLinhas(); 
        } catch (e) { 
            console.error("Erro API", e);
            if(selectAdd) selectAdd.innerHTML = '<option value="">Erro ao carregar</option>';
        }
    }

    // Filtra o select "lá de cima" (Painel Adicionar)
    function filtrarDisjuntoresLocalPainel() {
        const cat = document.getElementById('add_categoria').value;
        const select = document.getElementById('add_disjuntor');
        select.innerHTML = '';

        if(cat === 'Calcular'){
            select.add(new Option("Cálculo Automático (Manual)", "0"));
            return;
        }

        const validos = dadosDimensionamentoCache.filter(d => d.categoria === cat);
        if(validos.length === 0) {
            select.add(new Option("Nenhum padrão disponível", ""));
        } else {
            validos.forEach(d => {
                select.add(new Option(`${d.subcategoria} - ${d.corrente_disjuntor}A (${d.tipo_disjuntor})`, d.id));
            });
        }
    }

    // --- FUNÇÕES AUXILIARES DE FASE ---
    // [NOVO] Contador Global de Cargas (Quantas vezes cada fase foi usada)
    let cargasFases = { 'A': 0, 'B': 0, 'C': 0 };

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
        } 
        else if (categoria === 'B') {
            // Pega as DUAS fases mais "livres" (índice 0 e 1)
            const f1 = fasesOrdenadas[0];
            const f2 = fasesOrdenadas[1];
            
            cargasFases[f1]++;
            cargasFases[f2]++;
            
            // Retorna ordenado alfabeticamente (Ex: "AC" em vez de "CA")
            return [f1, f2].sort().join('');
        } 
        else if (categoria === 'T') {
            cargasFases['A']++; 
            cargasFases['B']++; 
            cargasFases['C']++;
            return 'ABC';
        }
        return '';
    }

    function getOpcoesFase(categoria) {
        if(categoria === 'M') return ['A', 'B', 'C'];
        // Garante que todas as combinações possíveis apareçam
        if(categoria === 'B') return ['AB', 'AC', 'BC', 'A', 'B']; 
        if(categoria === 'T') return ['ABC'];
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
        
        if(!dimId && categoria !== 'Calcular') return;

        // Prepara objeto com dados iniciais
        let dadosIniciais = { 
            classe: classe, 
            categoria: categoria, 
            dimensionamento_id: dimId 
        };

        // Se tiver um padrão selecionado, pega os detalhes técnicos
        if(dimId !== "0" && dimId !== "") {
            const dim = dadosDimensionamentoCache.find(d => d.id == dimId);
            if(dim) {
                dadosIniciais.cabo = dim.cabo;
                dadosIniciais.eletroduto = dim.eletroduto;
                dadosIniciais.disjuntor = dim.corrente_disjuntor + "A";
                dadosIniciais.info_tecnica = `${dim.subcategoria} - ${dim.corrente_disjuntor}A`;
            }
        }

        if(emptyState) emptyState.remove();

        for(let i=0; i < qtd; i++) {
            // Calcula fase automática
            dadosIniciais.fases_especificas = getProximaFase(categoria);
            criarLinha(dadosIniciais);
        }
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
        if(placa) row.querySelector('.cls-placa').value = placa;
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
        if(dimId && dimId !== '0') {
            const dim = dadosDimensionamentoCache.find(d => d.id == dimId);
            if(dim) {
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

        if(cat === 'Calcular') {
            select.add(new Option("Cálculo Automático", "0"));
        } else {
            const validos = dadosDimensionamentoCache.filter(d => d.categoria === cat);
            if(validos.length === 0) {
                select.add(new Option("Sem padrão", ""));
            } else {
                validos.forEach(d => {
                    const opt = new Option(`${d.subcategoria} - ${d.corrente_disjuntor}A`, d.id);
                    if(d.id == valorSelecionado) opt.selected = true;
                    select.add(opt);
                });
            }
        }
        // Se mudou para uma categoria que não tem o ID antigo, força o valor para o primeiro ou vazio
        if(select.value !== valorSelecionado && valorSelecionado) {
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
            if(op === valorSelecionado) opt.selected = true;
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
            if(lista.children.length === 0) {
                lista.innerHTML = '<div id="empty-state" class="text-center py-8 text-gray-400 text-sm italic bg-gray-50 rounded-lg border border-dashed border-gray-200">Nenhuma medição adicionada ainda.</div>';
            } else {
                setTimeout(() => {
                    if(confirm("Deseja redistribuir as fases para rebalancear (A, B, C...)?")) {
                        redistribuirFasesDasUnidades();
                    }
                }, 100);
            }
        }
    }

    function redistribuirFasesDasUnidades() {
        // 1. Zera os contadores globais
        cargasFases = { 'A': 0, 'B': 0, 'C': 0 };

        const linhas = document.querySelectorAll('.unidade-row');
        linhas.forEach(row => {
            const cat = row.querySelector('.cls-categoria').value;
            // Só recalcula se não for 'Calcular' e se não tiver fase fixa manual (opcional, aqui vou recalcular tudo)
            if(cat !== 'Calcular') {
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
        cargasFases = { 'A': 0, 'B': 0, 'C': 0 };

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
            let dadosLinha = { ...item };
            
            // Se for legado e tiver repetições
            const qtd = parseInt(item.repeticoes) || 1;
            
            // Tenta recuperar dados técnicos do cache se faltar
            if(item.dimensionamento_id && (!item.cabo || !item.eletroduto)) {
                const dim = dadosDimensionamentoCache.find(d => d.id == item.dimensionamento_id);
                if(dim) {
                    dadosLinha.cabo = dim.cabo;
                    dadosLinha.eletroduto = dim.eletroduto;
                    dadosLinha.disjuntor = dim.corrente_disjuntor + "A";
                    dadosLinha.info_tecnica = `${dim.subcategoria} - ${dim.corrente_disjuntor}A`;
                }
            }

            for(let i=0; i<qtd; i++) {
                // Se não tiver fase salva, calcula
                if(!dadosLinha.fases_especificas) {
                    dadosLinha.fases_especificas = getProximaFase(dadosLinha.categoria || item.categoria);
                }
                criarLinha(dadosLinha);
            }
        });
    }

    // Inicialização
    document.addEventListener('DOMContentLoaded', function() {
        // ... (ViaCEP igual) ...
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

        <?php if(isset($projeto_recuperado)): ?>
            const dadosRecuperados = <?= $projeto_recuperado ?>;
            setTimeout(() => { preencherTudo(dadosRecuperados); }, 500);
        <?php endif; ?>
    });
    
    // Funções Importação Modal
    function abrirModalImportacao() { document.getElementById('modal-importacao').classList.remove('hidden'); }
    function fecharModalImportacao() { document.getElementById('modal-importacao').classList.add('hidden'); document.getElementById('file-upload').value = ''; }
    function processarImportacao() {
        const file = document.getElementById('file-upload').files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = async function(e) {
            try {
                const data = JSON.parse(e.target.result);
                await preencherTudo(data);
                fecharModalImportacao();
            } catch (error) { console.error(error); alert("Erro arquivo."); }
        };
        reader.readAsText(file);
    }
</script>
</body>
</html>