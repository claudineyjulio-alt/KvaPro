<?= $this->extend('layouts/kvapro_layout') ?>

<?= $this->section('title') ?>Home<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-body">
    <div id="mobileQuickAccess" class="mobile-quick-access"></div>

    <link rel="stylesheet" href="<?= base_url('assets/css/projeto.css') ?>">

    <input type="hidden" id="csrf_token_name" value="<?= csrf_token() ?>">
    <input type="hidden" id="csrf_hash" value="<?= csrf_hash() ?>">

    <div class="page-actions">
        <a href="<?= base_url('dashboard') ?>" class="btn-action btn-outline">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <button type="button" onclick="abrirModalImportacao()" class="btn-action btn-outline" style="border-color: var(--navy-accent); color: var(--navy-dark);">
            <i class="fas fa-file-import"></i> Importar Projeto .kva
        </button>
    </div>

    <form id="form-projeto" action="<?= base_url('projeto/salvar') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="form-card">
            <div class="form-card-header">
                <span class="step-number">1</span>
                <h2 class="step-title">IDENTIFICAÇÃO</h2>
            </div>
            <div class="form-card-body">
                <div class="form-grid">
                    <div class="col-4">
                        <label class="form-label">Título da Obra</label>
                        <input type="text" name="titulo_obra" id="titulo_obra" required class="form-control">
                    </div>
                    <div class="col-5">
                        <label class="form-label">Nome do Cliente</label>
                        <input type="text" name="cliente_nome" id="cliente_nome" required class="form-control">
                    </div>
                    <div class="col-3">
                        <label class="form-label">Tipo de Obra</label>
                        <select name="tipo_obra" id="tipo_obra" class="form-control">
                            <option value="Nova Ligacao">Nova Ligação</option>
                            <option value="Aumento de Carga">Aumento de Carga</option>
                            <option value="Reforma">Reforma</option>
                            <option value="Provisoria">Ligação Provisória</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="section-subtitle">Localização</div>
                    </div>

                    <div class="col-3"><label class="form-label">CEP</label><input type="text" id="cep" name="cep" maxlength="9" class="form-control"></div>
                    <div class="col-6"><label class="form-label">Logradouro</label><input type="text" id="logradouro" name="logradouro" class="form-control"></div>
                    <div class="col-3"><label class="form-label">Nº</label><input type="text" id="numero" name="numero" class="form-control"></div>
                    <div class="col-4"><label class="form-label">Bairro</label><input type="text" id="bairro" name="bairro" class="form-control"></div>
                    <div class="col-4"><label class="form-label">Cidade</label><input type="text" id="cidade" name="cidade" class="form-control"></div>
                    <div class="col-1"><label class="form-label">UF</label><input type="text" id="uf" name="uf" class="form-control" style="text-transform: uppercase;"></div>
                    <div class="col-3"><label class="form-label">Zona</label>
                        <select name="zona" id="zona" class="form-control">
                            <option value="Urbano">Urbana</option>
                            <option value="Rural">Rural</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="form-card-header">
                <span class="step-number">2</span>
                <h2 class="step-title">DADOS GERAIS</h2>
            </div>
            <div class="form-card-body">
                <div class="form-grid">
                    <div class="col-3">
                        <label class="form-label">Concessionária *</label>
                        <select name="concessionaria_id" id="concessionaria_id" onchange="atualizarDimensionamentoGlobal()" required class="form-control">
                            <option value="">Selecione...</option>
                            <?php foreach ($concessionarias as $con): ?>
                                <option value="<?= $con['id'] ?>"><?= $con['nome'] ?> - <?= $con['estado'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Tensão de Atendimento *</label>
                        <select name="tensao_id" id="tensao_id" onchange="atualizarDimensionamentoGlobal()" required class="form-control">
                            <option value="">Selecione...</option>
                            <?php foreach ($tensoes as $ten): ?>
                                <option value="<?= $ten['id'] ?>">[<?= $ten['classe'] ?>] <?= $ten['descricao'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Tipo de Ramal</label>
                        <select name="tipo_ramal" id="tipo_ramal" class="form-control">
                            <option value="Aereo">Aéreo</option>
                            <option value="Subterraneo">Subterrâneo</option>
                        </select>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Localização Medidor</label>
                        <select name="localizacao_medidor" id="localizacao_medidor" class="form-control">
                            <option value="Muro Frontal">Muro Frontal</option>
                            <option value="Poste Auxiliar">Poste Auxiliar</option>
                            <option value="Fachada">Fachada</option>
                            <option value="Pontalete">Pontalete</option>
                        </select>
                    </div>
                    <div class="col-12" id="div_travessia">
                        <label class="form-label">Travessia</label>
                        <select name="travessia" id="travessia" class="form-control">
                            <option value="pedestre">Atravessa a calçada</option>
                            <option value="rua">Atravessa a rua</option>
                            <option value="garagem">Atravessa entrada de veiculos</option>
                            <option value="rodovia">Atravessa a rodovia</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>


        <div class="form-card">
            <div class="form-card-header">
                <span class="step-number">3</span>
                <h2 class="step-title">MEDIÇÕES</h2>
            </div>

            <div class="add-panel">
                <h3 class="section-subtitle">Adicionar unidades</h3>
                <div class="form-grid">
                    <div class="col-4">
                        <label class="form-label">Classe</label>
                        <select id="add_classe" class="form-control">
                            <option value="Residencial">Residencial</option>
                            <option value="Comercial">Comercial</option>
                            <option value="Industrial">Industrial</option>
                            <option value="B. Incendio">Bomba Incêndio</option>
                            <option value="Condominio">Condomínio</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="form-label">Tipo</label>
                        <select id="add_categoria" class="form-control" onchange="filtrarDisjuntoresLocalPainel()">
                            <option value="1">Monofásica</option>
                            <option value="2">Bifásica</option>
                            <option value="3">Trifásica</option>
                            <option value="Calcular">A Calcular</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="form-label">Disjuntor</label>
                        <select id="add_disjuntor" class="form-control" onchange="preencherDadosCabosPainel()">
                            <option value="">Informe os dados da concessionária</option>
                        </select>
                    </div>

                    <div class="col-3">
                        <label class="form-label">Cabos por fase</label>
                        <input type="text" id="qtd_fase_add" value="1" required class="form-control">
                    </div>
                    <div class="col-3">
                        <label class="form-label">Seção fase (mm²)</label>
                        <input type="text" id="fase_add" placeholder="Ex: 16" required class="form-control">
                    </div>
                    <div class="col-3">
                        <label class="form-label">Seção neutro (mm²)</label>
                        <input type="text" id="neutro_add" placeholder="Ex: 16" required class="form-control">
                    </div>
                    <div class="col-3">
                        <label class="form-label">Eletroduto (Ø")</label>
                        <input type="text" id="eletroduto_add" placeholder='Ex: 2' required class="form-control">
                    </div>

                    <div class="col-3">
                        <label class="form-label">Prefixo</label>
                        <input type="text" id="prefixo_add" value='AP - ' oninput="this.value = this.value.toUpperCase();" required class="form-control">
                    </div>
                    <div class="col-3">
                        <label class="form-label">Numero inicial</label>
                        <input type="text" id="numero_inicial_add" value='101' required class="form-control">
                    </div>
                    <div class="col-3">
                        <label class="form-label">Incremento</label>
                        <input type="text" id="incremento_add" value='1' required class="form-control">
                    </div>

                    <div class="col-3">
                        <label class="form-label">Quantidade</label>
                        <div style="display: flex; gap: 5px;">
                            <input type="number" id="add_qtd" value="1" min="1" class="form-control">
                            <button type="button" onclick="adicionarNaLista()" class="btn-add">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-card-body">
                <h3 class="section-subtitle">Unidades Adicionadas</h3>
                <input type="hidden" name="total_medidores" id="input_total_medidores" value="1">
                <div id="lista-unidades">
                    <div id="empty-state" style="text-align: center; padding: 30px; color: #999; border: 2px dashed #eee; border-radius: 6px;">
                        Nenhuma medição adicionada ainda.
                    </div>
                </div>
            </div>
        </div>


        <div class="form-card">
            <div class="form-card-header">
                <span class="step-number">4</span>
                <h2 class="step-title">ALIMENTADOR GERAL</h2>
            </div>
            <div class="form-card-body">
                <div class="form-grid">
                    <!-- <div class="col-12">
                        <div class="section-subtitle">Dados da Entrada</div>
                    </div> -->

                    <div class="col-2">
                        <label class="form-label">Cabos por fase</label>
                        <input type="text" name="qtd_fase" id="qtd_fase" value="1" required class="form-control">
                    </div>
                    <div class="col-2">
                        <label class="form-label">Seção fase (mm²)</label>
                        <input type="text" name="entrada_fase" id="entrada_fase" placeholder="Ex: 16" required class="form-control">
                    </div>
                    <div class="col-2">
                        <label class="form-label">Seção neutro (mm²)</label>
                        <input type="text" name="entrada_neutro" id="entrada_neutro" placeholder="Ex: 16" required class="form-control">
                    </div>
                    <div class="col-2">
                        <label class="form-label">Eletroduto (Ø")</label>
                        <input type="text" name="entrada_eletroduto" id="entrada_eletroduto" placeholder='Ex: 2' required class="form-control">
                    </div>
                    <div class="col-2">
                        <label class="form-label">Tipo (Geral)</label>
                        <select name="tipo_geral" id="tipo_geral" class="form-control">
                            <option value="3">Trifásico</option>
                            <option value="2">Bifásico</option>
                            <option value="1">Monofásico</option>
                        </select>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Disjuntor Geral (A)</label>
                        <input type="text" name="entrada_disjuntor" id="entrada_disjuntor" placeholder="Ex: 100" required class="form-control">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-card">
            <div class="form-card-header">
                <span class="step-number">5</span>
                <h2 class="step-title">ATERRAMENTO E DPS</h2>
            </div>
            <div class="form-card-body">
                <div class="form-grid">

                    <div class="col-12">
                        <div class="section-subtitle">Proteção (DPS)</div>
                    </div>
                    <div class="col-4">
                        <label class="form-label">Tensão DPS</label>
                        <input type="text" name="dps_tensao" id="dps_tensao" value="275" class="form-control">
                    </div>
                    <div class="col-4">
                        <label class="form-label">Capacidade min. (kA)</label>
                        <input type="text" name="dps_ka" id="dps_ka" value="20" class="form-control">
                    </div>
                    <div class="col-4">
                        <label class="form-label">Cabo DPS (mm²)</label>
                        <input type="text" name="dps_cabo" id="dps_cabo" value="10" class="form-control">
                    </div>

                    <div class="col-12">
                        <div class="section-subtitle">Aterramento</div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Seção Terra (mm²)</label>
                        <input type="text" name="terra_cabo" id="terra_cabo" value="16" class="form-control">
                    </div>
                    <div class="col-2">
                        <label class="form-label">Eletroduto Terra (Ø")</label>
                        <input type="text" name="terra_tubo" id="terra_tubo" value='3/4' class="form-control">
                    </div>
                    <div class="col-2">
                        <label class="form-label">Nº de Hastes</label>
                        <select name="terra_hastes" id="terra_hastes" class="form-control">
                            <option value="1">1</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Tipo haste</label>
                        <select name="terra_tipo_hastes" id="terra_tipo_hastes" class="form-control">
                            <option value="16x2400mm">16x2400mm</option>
                        </select>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Caixa de inspeção</label>
                        <select name="terra_cx" id="terra_cx" class="form-control">
                            <option value="PVC">PVC</option>
                            <option value="Concreto">Concreto</option>
                        </select>
                    </div>
                </div>
            </div>


            <div style="text-align: center; margin-top: 20px; margin-bottom: 50px;">
                <button type="submit" class="btn-action btn-success" style="padding: 15px 30px; font-size: 1rem;">
                    <i class="fas fa-check-circle"></i> Finalizar e Gerar Projeto
                </button>
            </div>
    </form>

    <div id="modal-importacao" class="custom-modal hidden">
        <div class="custom-modal-content">
            <h3 class="modal-title" style="margin-bottom: 15px;">Importar Projetos .KVA</h3>
            <input type="file" id="file-upload" accept=".kva,.json" class="form-control" style="margin-bottom: 20px;">
            <div style="text-align: right; display: flex; justify-content: flex-end; gap: 10px;">
                <button onclick="fecharModalImportacao()" class="btn-action btn-outline">Cancelar</button>
                <button type="button" onclick="processarImportacao()" class="btn-action btn-success">Carregar</button>
            </div>
        </div>
    </div>


    <template id="tpl-unidade">
        <div class="unidade-row">
            <button type="button" onclick="removerLinha(this)" class="btn-remove-row" title="Excluir Medição">
                <i class="fas fa-trash-alt"></i>
            </button>

            <div style="margin-bottom: 15px; font-weight: bold; color: var(--navy-dark); font-size: 0.9rem; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                Dados do <span class="medidor-num">#</span>º Medidor
            </div>

            <div class="form-grid">

                <div class="col-3">
                    <label class="form-label">Classe</label>
                    <select name="unidades[INDEX][classe]" class="cls-classe form-control">
                        <option value="Residencial">Residencial</option>
                        <option value="Comercial">Comercial</option>
                        <option value="Industrial">Industrial</option>
                        <option value="B. Incendio">Bomba Incêndio</option>
                        <option value="Condominio">Condomínio</option>
                    </select>
                </div>
                <div class="col-2">
                    <label class="form-label">Nº de fases</label>
                    <select name="unidades[INDEX][categoria]" class="cls-categoria form-control" onchange="aoMudarCategoriaDaLinha(this)">
                        <option value="1">Monofásica</option>
                        <option value="2">Bifásica</option>
                        <option value="3">Trifásica</option>
                        <option value="Calcular">A Calcular</option>
                    </select>
                </div>
                <div class="col-5">
                    <label class="form-label">Categoria</label>
                    <select name="unidades[INDEX][dimensionamento_id]" class="cls-dim-id form-control" onchange="aoMudarPadraoDaLinha(this)">
                    </select>
                </div>
                <div class="col-2">
                    <label class="form-label">Fases</label>
                    <select name="unidades[INDEX][fases_especificas]" class="cls-fases form-control">
                    </select>
                </div>

                <div class="col-2">
                    <label class="form-label">Cabos/fase</label>
                    <input type="text" name="unidades[INDEX][qtd_fase_add]" class="cls-qtd-fase form-control" placeholder="Qtd">
                </div>
                <div class="col-2">
                    <label class="form-label">Fase (mm²)</label>
                    <input type="text" name="unidades[INDEX][fase]" class="cls-fase form-control" placeholder="Fase">
                </div>
                <div class="col-2">
                    <label class="form-label">Neutro (mm²)</label>
                    <input type="text" name="unidades[INDEX][neutro]" class="cls-neutro form-control" placeholder="Neutro">
                </div>
                <div class="col-3">
                    <label class="form-label">Eletroduto</label>
                    <input type="text" name="unidades[INDEX][eletroduto]" class="cls-eletroduto form-control" placeholder="Diâmetro">
                </div>
                <div class="col-3">
                    <label class="form-label">Disjuntor</label>
                    <input type="text" name="unidades[INDEX][disjuntor]" class="cls-disjuntor form-control" placeholder="Corrente">
                </div>

                <div class="col-3">
                    <label class="form-label">Identificação</label>
                    <input type="text" name="unidades[INDEX][placa]" class="cls-placa form-control" placeholder="Ex: Casa 1" style="font-weight: bold;">
                </div>
                <div class="col-3">
                    <label class="form-label">Nº UC (Opcional)</label>
                    <input type="text" name="unidades[INDEX][numero_uc]" class="cls-uc form-control" placeholder="Ex: 112121">
                </div>
                <div class="col-6">
                    <label class="form-label">Observações</label>
                    <input type="text" name="unidades[INDEX][observacao]" class="cls-obs form-control" placeholder="Detalhes adicionais...">
                </div>
            </div>

            <input type="hidden" class="inp-info-tec" name="unidades[INDEX][info_tecnica]">
            <!-- <input type="hidden" name="unidades[INDEX][mbt]" class="cls-mbt" value="1"> -->
        </div>
    </template>

    <!-- <template id="tpl-unidade">
        <div class="unidade-row">
            <button type="button" onclick="removerLinha(this)" class="btn-remove-row" title="Excluir Medição">
                <i class="fas fa-trash-alt"></i>
            </button>

            <div style="margin-bottom: 15px; font-weight: bold; color: var(--navy-dark); font-size: 0.9rem; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                Dados do <span class="medidor-num">#</span>º Medidor
            </div>

            <div class="form-grid">

                <div class="col-3">
                    <label class="form-label">Classe</label>
                    <select name="unidades[INDEX][classe]" class="cls-classe form-control">
                        <option value="Residencial">Residencial</option>
                        <option value="Comercial">Comercial</option>
                        <option value="Industrial">Industrial</option>
                        <option value="B. Incendio">Bomba Incêndio</option>
                        <option value="Condominio">Condomínio</option>
                    </select>
                </div>
                <div class="col-2">
                    <label class="form-label">Nº de fases</label>
                    <select name="unidades[INDEX][categoria]" class="cls-categoria form-control" onchange="aoMudarCategoriaDaLinha(this)">
                        <option value="M">Monofásica</option>
                        <option value="B">Bifásica</option>
                        <option value="T">Trifásica</option>
                        <option value="Calcular">A Calcular</option>
                    </select>
                </div>
                <div class="col-5">
                    <label class="form-label">Categoria</label>
                    <select name="unidades[INDEX][dimensionamento_id]" class="cls-dim-id form-control" onchange="aoMudarPadraoDaLinha(this)">
                    </select>
                </div>
                <div class="col-2">
                    <label class="form-label">Fases</label>
                    <select name="unidades[INDEX][fases_especificas]" class="cls-fases form-control">
                    </select>
                </div>

                <div class="col-4">
                    <label class="form-label">Cabo Ramal</label>
                    <input type="text" name="unidades[INDEX][cabo]" class="cls-cabo form-control" placeholder="Automático...">
                </div>
                <div class="col-4">
                    <label class="form-label">Eletroduto</label>
                    <input type="text" name="unidades[INDEX][eletroduto]" class="cls-eletroduto form-control" placeholder="Automático...">
                </div>
                <div class="col-4">
                    <label class="form-label">Disjuntor</label>
                    <input type="text" name="unidades[INDEX][disjuntor]" class="cls-disjuntor form-control" placeholder="Automático...">
                </div>

                <div class="col-3">
                    <label class="form-label">Identificação</label>
                    <input type="text" name="unidades[INDEX][placa]" class="cls-placa form-control" placeholder="Ex: Casa 1" style="font-weight: bold;">
                </div>
                <div class="col-3">
                    <label class="form-label">Nº UC (Opcional)</label>
                    <input type="text" name="unidades[INDEX][numero_uc]" class="cls-uc form-control" placeholder="Ex: 112121">
                </div>
                <div class="col-6">
                    <label class="form-label">Observações</label>
                    <input type="text" name="unidades[INDEX][observacao]" class="cls-obs form-control" placeholder="Detalhes adicionais...">
                </div>
            </div>

            <input type="hidden" class="inp-info-tec" name="unidades[INDEX][info_tecnica]">
        </div>
    </template> -->

</div>

<script>
    const API_URL_DIMENSIONAMENTO = "<?= base_url('projeto/api/dimensionamento') ?>";
</script>
<script id="dados-recuperados" type="application/json">
    <?= isset($projeto_recuperado) ? $projeto_recuperado : 'null' ?>
</script>
<script src="<?= base_url('assets/js/projeto-novo.js') ?>"></script>

<?= $this->endSection() ?>