<?= $this->extend('layouts/kvapro_layout') ?>

<?= $this->section('title') ?><?= isset($item) ? 'Editar' : 'Novo' ?> Dimensionamento<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-body">

    <div class="page-actions">
        <a href="<?= base_url('admin/dimensionamento') ?>" class="btn-action btn-outline">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <form method="post" action="<?= current_url() ?>">
        <?= csrf_field() ?>

        <?php if (isset($item['id'])): ?>
            <input type="hidden" name="id" value="<?= $item['id'] ?>">
        <?php endif; ?>

        <div class="form-card">
            <div class="form-card-header">
                <h2 class="step-title">
                    <i class="fas fa-sliders-h" style="color: var(--navy-accent); margin-right: 8px;"></i>
                    <?= isset($item) ? 'Editar Padrão de Dimensionamento' : 'Cadastrar Padrão de Dimensionamento' ?>
                </h2>
            </div>

            <div class="form-card-body">
                <div class="section-subtitle">Norma e Referência</div>
                <div class="form-grid" style="margin-bottom: 25px;">
                    <div class="col-4">
                        <label class="form-label">Concessionária</label>
                        <select name="id_concessionaria" class="form-control" required>
                            <option value="">Selecione...</option>
                            <?php if (isset($concessionarias)): ?>
                                <?php foreach ($concessionarias as $conc): ?>
                                    <option value="<?= $conc['id'] ?>" <?= (isset($item) && $item['id_concessionaria'] == $conc['id']) ? 'selected' : '' ?>>
                                        <?= esc($conc['nome']) ?> (<?= esc($conc['estado']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="form-label">Tensão / Classe</label>
                        <select name="id_tensao" class="form-control" required>
                            <option value="">Selecione...</option>
                            <?php if (isset($tensoes)): ?>
                                <?php foreach ($tensoes as $tensao): ?>
                                    <option value="<?= $tensao['id'] ?>" <?= (isset($item) && $item['id_tensao'] == $tensao['id']) ? 'selected' : '' ?>>
                                        <?= esc($tensao['descricao']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Norma (Opcional)</label>
                        <input type="text" name="norma" class="form-control" value="<?= isset($item) ? esc($item['norma']) : '' ?>" placeholder="Ex: NDU001">
                    </div>
                    <div class="col-2">
                        <label class="form-label">Tabela (Opcional)</label>
                        <input type="text" name="tabela_norma" class="form-control" value="<?= isset($item) ? esc($item['tabela_norma']) : '' ?>" placeholder="Ex: Tabela 3">
                    </div>
                </div>

                <div class="section-subtitle">Classificação e Carga</div>
                <div class="form-grid" style="margin-bottom: 25px;">
                    <div class="col-2">
                        <label class="form-label">Categoria</label>
                        <select name="categoria" class="form-control" required>
                            <option value="M" <?= (isset($item) && $item['categoria'] == 'M') ? 'selected' : '' ?>>Mono (M)</option>
                            <option value="B" <?= (isset($item) && $item['categoria'] == 'B') ? 'selected' : '' ?>>Bi (B)</option>
                            <option value="T" <?= (isset($item) && $item['categoria'] == 'T') ? 'selected' : '' ?>>Tri (T)</option>
                        </select>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Subcategoria</label>
                        <input type="text" name="subcategoria" onkeyup="this.value=this.value.toUpperCase()" class="form-control" value="<?= isset($item) ? esc($item['subcategoria']) : '' ?>" required placeholder="Ex: M1">
                    </div>
                    <div class="col-2">
                        <label class="form-label">Carga Mínima</label>
                        <input type="number" step="0.01" name="pot_min" class="form-control" value="<?= isset($item) ? esc($item['pot_min']) : '0.00' ?>" required>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Carga Máxima</label>
                        <input type="number" step="0.01" name="pot_max" class="form-control" value="<?= isset($item) ? esc($item['pot_max']) : '' ?>" required>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Unidade</label>
                        <select name="unidade" class="form-control">
                            <option value="kW" <?= (isset($item) && $item['unidade'] == 'kW') ? 'selected' : '' ?>>kW</option>
                            <option value="kVA" <?= (isset($item) && $item['unidade'] == 'kVA') ? 'selected' : '' ?>>kVA</option>
                            <option value="VA" <?= (isset($item) && $item['unidade'] == 'VA') ? 'selected' : '' ?>>VA</option>
                            <option value="W" <?= (isset($item) && $item['unidade'] == 'W') ? 'selected' : '' ?>>W</option>
                        </select>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Cálculo Baseado Em</label>
                        <select name="carga_a_considerar" class="form-control" required>
                            <option value="instalada" <?= (isset($item) && $item['carga_a_considerar'] == 'instalada') ? 'selected' : '' ?>>Carga Instalada</option>
                            <option value="demanda" <?= (isset($item) && $item['carga_a_considerar'] == 'demanda') ? 'selected' : '' ?>>Demanda (kVA)</option>
                        </select>
                    </div>
                </div>

                <div class="section-subtitle">Proteção e Condutores (Aéreo e Subterrâneo)</div>
                <div class="form-grid" style="margin-bottom: 25px;">
                    <div class="col-2">
                        <label class="form-label">Disjuntor (A)</label>
                        <input type="number" name="corrente_disjuntor" class="form-control" value="<?= isset($item) ? esc($item['corrente_disjuntor']) : '' ?>" required>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Tipo Disjuntor</label>
                        <input type="text" name="tipo_disjuntor" class="form-control" value="<?= isset($item) ? esc($item['tipo_disjuntor']) : 'Termomagnético' ?>">
                    </div>
                    <div class="col-2">
                        <label class="form-label">Qtd. Cabos/Fase</label>
                        <input type="number" name="qtd_cabos_fase" class="form-control" value="<?= isset($item) ? esc($item['qtd_cabos_fase']) : '1' ?>" required>
                    </div>

                    <div class="col-3">
                        <label class="form-label">Cabo Aéreo (Fase/Neutro/Terra)</label>
                        <select name="cabo_aereo_id" class="form-control">
                            <option value="">Selecione o Cabo...</option>
                            <?php if (isset($cabos)): ?>
                                <?php foreach ($cabos as $cabo): ?>
                                    <option value="<?= $cabo['id'] ?>" <?= (isset($item) && $item['cabo_aereo_id'] == $cabo['id']) ? 'selected' : '' ?>>
                                        <?= esc($cabo['fase']) ?> / <?= esc($cabo['neutro']) ?> / <?= esc($cabo['terra']) ?> mm² (<?= esc($cabo['isolacao']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Cabo Subterrâneo (Fase/Neutro/Terra)</label>
                        <select name="cabo_subterraneo_id" class="form-control">
                            <option value="">Selecione o Cabo...</option>
                            <?php if (isset($cabos)): ?>
                                <?php foreach ($cabos as $cabo): ?>
                                    <option value="<?= $cabo['id'] ?>" <?= (isset($item) && $item['cabo_subterraneo_id'] == $cabo['id']) ? 'selected' : '' ?>>
                                        <?= esc($cabo['fase']) ?> / <?= esc($cabo['neutro']) ?> / <?= esc($cabo['terra']) ?> mm² (<?= esc($cabo['isolacao']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="col-2 mt-3">
                        <label class="form-label">Eletroduto (Pol)</label>
                        <input type="text" name="eletroduto" class="form-control" value="<?= isset($item) ? esc($item['eletroduto']) : '' ?>" placeholder="Ex: 1.1/4">
                    </div>
                </div>

                <div class="form-grid" style="margin-bottom: 10px;">
                    <div class="col-12">
                        <label class="form-label">Resumo de Material Customizado (Aparece no Painel)</label>
                        <textarea name="descricao_material" class="form-control" rows="2" placeholder="Opcional. Ex: 3#16(16)mm² + Eletroduto 1.1/4\""><?= isset($item) ? esc($item['descricao_material']) : '' ?></textarea>
                    </div>
                </div>

                <div style=" text-align: right; border-top: 1px solid #eee; padding-top: 20px; margin-top: 20px;">
                    <a href="<?= base_url('admin/dimensionamento') ?>" class="btn-action btn-outline" style="margin-right: 10px;">
                        Cancelar
                    </a>
                    <button type="submit" class="btn-action btn-primary" style="padding: 10px 30px; font-size: 1rem;">
                        <i class="fas fa-save"></i> <?= isset($item) ? 'Atualizar Padrão' : 'Salvar Padrão' ?>
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>