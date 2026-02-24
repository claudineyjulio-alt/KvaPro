<!-- app/Views/admin/kits_lista.php -->
<?= $this->extend('layouts/kvapro_layout') ?>

<?= $this->section('title') ?>Kits de Montagem<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-body">

    <div class="page-actions">
        <a href="<?= base_url('dashboard') ?>" class="btn-action btn-outline">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <a href="<?= base_url('admin/materiais') ?>" class="btn-action btn-outline">
            <i class="fas fa-tools"></i> Gerenciar Materiais
        </a>
        <a href="<?= base_url('admin/kits/novo') ?>" class="btn-action btn-success">
            <i class="fas fa-plus"></i> Novo Kit
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
                <i class="fas fa-cubes" style="color: var(--navy-accent); margin-right: 8px;"></i> 
                KITS DE MONTAGEM
            </h2>
        </div>
        
        <div class="form-card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="kv-table">
                    <thead>
                        <tr>
                            <th style="width: 80px; text-align: center;">ID</th>
                            <th>Nome do Kit</th>
                            <th style="width: 250px;">Slug (Código)</th>
                            <th style="width: 120px; text-align: center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($kits)): ?>
                            <tr>
                                <td colspan="4" style="padding: 40px; text-align: center; color: #999;">
                                    <i class="fas fa-box-open fa-3x" style="color: #eee; margin-bottom: 15px; display: block;"></i>
                                    Nenhum kit cadastrado ainda. Clique em "Novo Kit" para começar.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($kits as $k): ?>
                            <tr>
                                <td style="text-align: center; color: #7f8c8d; font-family: monospace; font-size: 0.85rem;">
                                    #<?= $k['id'] ?>
                                </td>
                                
                                <td style="font-weight: 600; color: var(--navy-dark);">
                                    <?= esc($k['nome']) ?>
                                </td>
                                
                                <td>
                                    <span class="badge badge-blue" style="font-family: monospace; letter-spacing: 0.5px;">
                                        <?= esc($k['slug']) ?>
                                    </span>
                                </td>
                                
                                <td style="text-align: center;">
                                    <a href="<?= base_url('admin/kits/editar/'.$k['id']) ?>" style="color: #3498db; margin-right: 15px; font-size: 1.1rem;" title="Editar Kit (Adicionar Materiais)">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= base_url('admin/kits/excluir/'.$k['id']) ?>" onclick="return confirm('Tem certeza que deseja excluir este kit? Isso pode quebrar as Regras de Automação que dependem dele.')" style="color: #e74c3c; font-size: 1.1rem;" title="Excluir Kit">
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