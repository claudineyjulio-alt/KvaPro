<!-- app/Views/admin/materiais_form.php -->
 <?= $this->extend('layouts/kvapro_layout') ?>

<?= $this->section('title') ?><?= isset($item) ? 'Editar' : 'Novo' ?> Material<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-body">

    <div class="page-actions">
        <a href="<?= base_url('admin/materiais') ?>" class="btn-action btn-outline">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <form method="post" action="<?= base_url('admin/materiais/salvar') ?>">
        <?= csrf_field() ?>
        
        <?php if(isset($item['id'])): ?>
            <input type="hidden" name="id" value="<?= $item['id'] ?>">
        <?php endif; ?>

        <div class="form-card">
            <div class="form-card-header">
                <h2 class="step-title">
                    <i class="fas fa-tools" style="color: var(--navy-accent); margin-right: 8px;"></i> 
                    <?= isset($item) ? 'Editar Material' : 'Cadastrar Novo Material' ?>
                </h2>
            </div>
            
            <div class="form-card-body">
                
                <div class="section-subtitle">Detalhes do Item</div>
                
                <div class="form-grid" style="margin-bottom: 30px;">
                    <div class="col-8">
                        <label class="form-label">Descrição do Material</label>
                        <textarea name="descricao" rows="2" class="form-control" placeholder="Ex: Haste de Aterramento Cobreada Alta Camada 5/8x2400mm" required><?= isset($item) ? esc($item['descricao']) : '' ?></textarea>
                    </div>

                    <div class="col-4">
                        <label class="form-label">Unidade de Medida</label>
                        <select name="unidade" class="form-control" style="background-color: #f8f9fa;" required>
                            <?php 
                                $unidades = ['Unid', 'm', 'Peça', 'Conjunto', 'Barra', 'Kg', 'L', 'Par'];
                                $atual = $item['unidade'] ?? 'Unid';
                            ?>
                            <?php foreach($unidades as $u): ?>
                                <option value="<?= $u ?>" <?= $atual == $u ? 'selected' : '' ?>><?= $u ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="text-align: right; border-top: 1px solid #eee; padding-top: 20px;">
                    <a href="<?= base_url('admin/materiais') ?>" class="btn-action btn-outline" style="margin-right: 10px;">
                        Cancelar
                    </a>
                    <button type="submit" class="btn-action btn-primary" style="padding: 10px 30px; font-size: 1rem;">
                        <i class="fas fa-save"></i> <?= isset($item) ? 'Atualizar Material' : 'Salvar Material' ?>
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>