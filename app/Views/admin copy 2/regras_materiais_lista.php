<?= $this->extend('layouts/kvapro_layout') ?>

<?= $this->section('title') ?>Regras de Materiais<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-body">

    <div class="page-actions">
        <a href="<?= base_url('dashboard') ?>" class="btn-action btn-outline">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <a href="<?= base_url('admin/regras-materiais/novo') ?>" class="btn-action btn-success">
            <i class="fas fa-plus"></i> Nova Regra
        </a>
    </div>

    <?php if(session()->getFlashdata('sucesso')): ?>
        <div style="background-color: #e9f7ef; color: #27ae60; padding: 15px; margin-bottom: 20px; border-radius: 6px; border: 1px solid #c3e6cb; font-weight: 500;">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('sucesso') ?>
        </div>
    <?php endif; ?>

    <div class="form-card">
        <div class="form-card-header">
            <h2 class="step-title"><i class="fas fa-brain" style="color: var(--navy-accent); margin-right: 8px;"></i> MOTOR DE REGRAS (MATERIAIS)</h2>
        </div>
        
        <div class="form-card-body" style="padding: 0;"> 
            <div class="table-responsive">
                <table class="kv-table">
                    <thead>
                        <tr>
                            <th>Prio.</th>
                            <th>Categoria</th>
                            <th>Condição (Se...)</th>
                            <th>Resultado (Então Use...)</th>
                            <th style="text-align: center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($regras)): ?>
                            <?php foreach($regras as $r): ?>
                                <tr>
                                    <td>
                                        <?php 
                                            $badgeClass = 'badge-blue'; // Padrão
                                            if ($r['prioridade'] == 1) $badgeClass = 'badge-red'; // Alta
                                            elseif ($r['prioridade'] == 99) $badgeClass = 'badge-gray'; // Baixa
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= $r['prioridade'] ?>
                                        </span>
                                    </td>

                                    <td style="font-weight: 600; color: #555;">
                                        <?= strtoupper($r['tipo_kit']) ?>
                                    </td>

                                    <td>
                                        <div style="font-weight: bold; color: var(--navy-dark); margin-bottom: 4px;">
                                            <?= $r['descricao'] ?>
                                        </div>
                                        <code style="background: #f8f9fa; padding: 3px 6px; border-radius: 4px; color: #2980b9; font-size: 0.85rem; border: 1px solid #e1e8ed;">
                                            Se [<?= $r['variavel'] ?>] <?= $r['condicao'] ?> <?= $r['valor_min'] ?> <?= $r['valor_max'] ? 'e '.$r['valor_max'] : '' ?>
                                        </code>
                                    </td>

                                    <td style="color: #27ae60; font-weight: 600;">
                                        <i class="fas fa-box-open" style="margin-right: 5px;"></i>
                                        <?= $kitsMap[$r['kit_id']] ?? 'Kit ID: '.$r['kit_id'] ?>
                                    </td>

                                    <td style="text-align: center;">
                                        <a href="<?= base_url('admin/regras-materiais/editar/'.$r['id']) ?>" style="color: #3498db; margin-right: 15px; font-size: 1.1rem;" title="Editar Regra">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/regras-materiais/excluir/'.$r['id']) ?>" onclick="return confirm('Tem certeza que deseja excluir esta regra?')" style="color: #e74c3c; font-size: 1.1rem;" title="Excluir Regra">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="padding: 40px; text-align: center; color: #999;">
                                    <i class="fas fa-clipboard-list fa-3x" style="color: #eee; margin-bottom: 15px; display: block;"></i>
                                    Nenhuma regra cadastrada. Clique em "Nova Regra" para começar.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>