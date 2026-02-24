<!-- app/Views/admin/materiais_lista.php -->
<?= $this->extend('layouts/kvapro_layout') ?>

<?= $this->section('title') ?>Materiais Base<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-body">

    <div class="page-actions">
        <a href="<?= base_url('dashboard') ?>" class="btn-action btn-outline">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <a href="<?= base_url('admin/kits') ?>" class="btn-action btn-outline">
            <i class="fas fa-cubes"></i> Gerenciar Kits
        </a>
        <a href="<?= base_url('admin/materiais/novo') ?>" class="btn-action btn-success">
            <i class="fas fa-plus"></i> Novo Material
        </a>
    </div>

    <?php if(session()->getFlashdata('sucesso')): ?>
        <div style="background-color: #e9f7ef; color: #27ae60; padding: 15px; margin-bottom: 20px; border-radius: 6px; border: 1px solid #c3e6cb; font-weight: 500;">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('sucesso') ?>
        </div>
    <?php endif; ?>

    <div class="form-card">
        <div class="form-card-header">
            <h2 class="step-title">
                <i class="fas fa-tools" style="color: var(--navy-accent); margin-right: 8px;"></i> 
                MATERIAIS BASE
            </h2>
        </div>
        
        <div class="form-card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="kv-table">
                    <thead>
                        <tr>
                            <th style="width: 80px; text-align: center;">ID</th>
                            <th>Descrição do Material</th>
                            <th style="width: 120px; text-align: center;">Unidade</th>
                            <th style="width: 120px; text-align: center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($materiais)): ?>
                            <tr>
                                <td colspan="4" style="padding: 40px; text-align: center; color: #999;">
                                    <i class="fas fa-box-open fa-3x" style="color: #eee; margin-bottom: 15px; display: block;"></i>
                                    Nenhum material cadastrado ainda. Clique em "Novo Material" para começar.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($materiais as $m): ?>
                            <tr>
                                <td style="text-align: center; color: #7f8c8d; font-family: monospace; font-size: 0.85rem;">
                                    #<?= $m['id'] ?>
                                </td>
                                
                                <td style="font-weight: 600; color: var(--navy-dark);">
                                    <?= esc($m['descricao']) ?>
                                </td>
                                
                                <td style="text-align: center;">
                                    <span class="badge badge-gray"><?= esc($m['unidade']) ?></span>
                                </td>
                                
                                <td style="text-align: center;">
                                    <a href="<?= base_url('admin/materiais/editar/'.$m['id']) ?>" style="color: #3498db; margin-right: 15px; font-size: 1.1rem;" title="Editar Material">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= base_url('admin/materiais/excluir/'.$m['id']) ?>" onclick="return confirm('Tem certeza que deseja excluir este material? Isso pode quebrar os kits que dependem dele.')" style="color: #e74c3c; font-size: 1.1rem;" title="Excluir Material">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>