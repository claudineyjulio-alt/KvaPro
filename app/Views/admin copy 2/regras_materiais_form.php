<!-- app/Views/admin/regras_materiais_form.php -->
<?= $this->extend('layouts/kvapro_layout') ?>

<?= $this->section('title') ?><?= isset($item) ? 'Editar' : 'Nova' ?> Regra<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-body">

    <div class="page-actions">
        <a href="<?= base_url('admin/regras-materiais') ?>" class="btn-action btn-outline">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <form method="post" action="<?= base_url('admin/regras-materiais/salvar') ?>">
        <?= csrf_field() ?>

        <?php if (isset($item['id'])): ?>
            <input type="hidden" name="id" value="<?= $item['id'] ?>">
        <?php endif; ?>

        <input type="hidden" name="concessionaria_id" value="1">

        <div class="form-card">
            <div class="form-card-header">
                <h2 class="step-title">
                    <i class="fas fa-brain" style="color: var(--navy-accent); margin-right: 8px;"></i>
                    <?= isset($item) ? 'Editar Regra de Decisão' : 'Criar Nova Regra de Decisão' ?>
                </h2>
            </div>

            <div class="form-card-body">

                <div class="section-subtitle">Configurações Básicas</div>
                <div class="form-grid" style="margin-bottom: 30px;">
                    <div class="col-4">
                        <label class="form-label">Categoria (Tipo do Kit)</label>
                        <select name="tipo_kit" class="form-control" style="background-color: #f8f9fa;" required>
                            <?php $tipos = ['infra' => 'Infraestrutura', 'aterramento' => 'Aterramento', 'medicao' => 'Medição', 'entrada' => 'Entrada']; ?>
                            <?php foreach ($tipos as $val => $label): ?>
                                <option value="<?= $val ?>" <?= (isset($item) && $item['tipo_kit'] == $val) ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-4">
                        <label class="form-label">Prioridade <span style="font-weight: normal; color: #999; font-size: 0.75rem;">(1=Máx, 99=Padrão)</span></label>
                        <input type="number" name="prioridade" value="<?= $item['prioridade'] ?? '99' ?>" class="form-control" required>
                    </div>

                    <div class="col-4">
                        <label class="form-label">Descrição (Para Humanos)</label>
                        <input type="text" name="descricao" value="<?= $item['descricao'] ?? '' ?>" class="form-control" placeholder="Ex: Poste de 7m se for Rua" required>
                    </div>
                </div>

                <div style="background-color: #f0f8ff; border: 1px solid #cce5ff; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                    <h3 style="color: #004085; font-size: 0.95rem; font-weight: bold; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                        <span style="background: #007bff; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem;">SE</span>
                        A condição do projeto for:
                    </h3>

                    <div class="form-grid">
                        <div class="col-4">
                            <label class="form-label">Variável do Projeto</label>
                            <select name="variavel" class="form-control" required>
                                <option value="">Selecione a variável...</option>

                                <?php if (isset($variaveisProjeto)): ?>
                                    <?php foreach ($variaveisProjeto as $grupo => $variaveis): ?>

                                        <optgroup label="--- <?= mb_strtoupper($grupo) ?> ---">

                                            <?php foreach ($variaveis as $chave => $label): ?>
                                                <?php $selecionado = (isset($item) && $item['variavel'] == $chave) ? 'selected' : ''; ?>
                                                <option value="<?= $chave ?>" <?= $selecionado ?>>
                                                    <?= $chave ?> (<?= $label ?>)
                                                </option>
                                            <?php endforeach; ?>

                                        </optgroup>

                                    <?php endforeach; ?>
                                <?php endif; ?>

                            </select>
                        </div>

                        <!-- <div class="col-4">
                            <label class="form-label">Variável do Projeto</label>
                            <select name="variavel" class="form-control">
                                <option value="padrao" <?= (isset($item) && $item['variavel'] == 'padrao') ? 'selected' : '' ?>>padrao (Regra Geral)</option>
                                <option value="travessia" <?= (isset($item) && $item['variavel'] == 'travessia') ? 'selected' : '' ?>>travessia</option>
                                <option value="fases" <?= (isset($item) && $item['variavel'] == 'fases') ? 'selected' : '' ?>>fases (nº)</option>
                                <option value="terra_hastes" <?= (isset($item) && $item['variavel'] == 'terra_hastes') ? 'selected' : '' ?>>terra_hastes</option>
                                <option value="entrada_cabo" <?= (isset($item) && $item['variavel'] == 'entrada_cabo') ? 'selected' : '' ?>>entrada_cabo</option>
                            </select>
                        </div> -->

                        <div class="col-3">
                            <label class="form-label">Lógica</label>
                            <select name="condicao" class="form-control" style="font-family: monospace;">
                                <option value="=" <?= (isset($item) && $item['condicao'] == '=') ? 'selected' : '' ?>>IGUAL (=)</option>
                                <option value="CONTEM" <?= (isset($item) && $item['condicao'] == 'CONTEM') ? 'selected' : '' ?>>CONTÉM</option>
                                <option value=">" <?= (isset($item) && $item['condicao'] == '>') ? 'selected' : '' ?>>MAIOR QUE (>)</option>
                                <option value=">=" <?= (isset($item) && $item['condicao'] == '>=') ? 'selected' : '' ?>>MAIOR/IGUAL (>=)</option>
                            </select>
                        </div>

                        <div class="col-3">
                            <label class="form-label">Valor Mínimo/Alvo</label>
                            <input type="text" name="valor_min" value="<?= $item['valor_min'] ?? '' ?>" class="form-control" placeholder="Ex: rua, 3, true">
                        </div>

                        <div class="col-2">
                            <label class="form-label">Valor Máx <span style="font-weight: normal; color: #999;">(Opcional)</span></label>
                            <input type="text" name="valor_max" value="<?= $item['valor_max'] ?? '' ?>" class="form-control">
                        </div>
                    </div>
                </div>

                <div style="background-color: #f8fff9; border: 1px solid #d4edda; border-radius: 8px; padding: 20px; margin-bottom: 30px;">
                    <h3 style="color: #155724; font-size: 0.95rem; font-weight: bold; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                        <span style="background: #28a745; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem;">ENTÃO</span>
                        Aplique este Kit de Materiais:
                    </h3>

                    <div class="form-grid">
                        <div class="col-12">
                            <select name="kit_id" class="form-control" style="font-weight: bold; border-color: #28a745; background-color: white;" required>
                                <option value="">Selecione um Kit da Biblioteca...</option>
                                <?php if (isset($kits)): ?>
                                    <?php foreach ($kits as $k): ?>
                                        <option value="<?= $k['id'] ?>" <?= (isset($item) && $item['kit_id'] == $k['id']) ? 'selected' : '' ?>>
                                            [<?= $k['slug'] ?>] <?= $k['nome'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div style="text-align: right; border-top: 1px solid #eee; padding-top: 20px;">
                    <button type="submit" class="btn-action btn-primary" style="padding: 12px 30px; font-size: 1rem;">
                        <i class="fas fa-save"></i> <?= isset($item) ? 'Atualizar Regra' : 'Salvar Nova Regra' ?>
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>