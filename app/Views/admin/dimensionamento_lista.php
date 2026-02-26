<?= $this->extend('layouts/kvapro_layout') ?>

<?= $this->section('title') ?>Padrões de Dimensionamento<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-body">

    <div class="page-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; color: var(--navy-dark); font-size: 1.5rem;">
            <i class="fas fa-bolt" style="color: #f1c40f; margin-right: 10px;"></i> Tabelas de Dimensionamento
        </h2>
        <a href="<?= base_url('admin/dimensionamento/novo') ?>" class="btn-action btn-primary">
            <i class="fas fa-plus"></i> Novo Padrão
        </a>
    </div>

    <?php if (session()->has('sucesso')): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #28a745;">
            <i class="fas fa-check-circle"></i> <?= session('sucesso') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->has('erro')): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #dc3545;">
            <i class="fas fa-exclamation-triangle"></i> <?= session('erro') ?>
        </div>
    <?php endif; ?>

    <div style="background: #fff; border: 1px solid #eee; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
        <div class="table-responsive">
            <table class="kv-table" style="width: 100%; margin: 0;">
                <thead>
                    <tr>
                        <!-- <th style="width: 80px; text-align: center;">Cat.</th> -->
                        <th>Categoria</th>
                        <th>Potência (Min - Máx)</th>
                        <th>Disjuntor</th>
                        <th>Cabos Aéreos</th>
                        <th>Cabos Subterrâneos</th>
                        <th style="width: 120px; text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dimensionamentos)): ?>
                        <?php foreach ($dimensionamentos as $item): ?>
                            <tr>
                                <!-- <td style="text-align: center; font-weight: bold; color: var(--navy-accent);">
                                    <?= esc($item['categoria']) ?>
                                </td> -->
                                <td>
                                    <span style="background: #f1f5f9; padding: 3px 8px; border-radius: 4px; font-weight: 600; font-size: 0.85rem; border: 1px solid #e2e8f0;">
                                        <?= esc($item['subcategoria']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= number_format($item['pot_min'], 2, ',', '.') ?> a <?= number_format($item['pot_max'], 2, ',', '.') ?> <?= esc($item['unidade']) ?>
                                </td>
                                <td><?= esc($item['corrente_disjuntor']) ?>A <span style="font-size: 0.75rem; color: #7f8c8d;">(<?= esc($item['tipo_disjuntor']) ?>)</span></td>
                                <td>
                                    <?php if ($item['aereo_fase']): ?>
                                        <div style="font-size: 0.95rem; color: #2980b9; font-weight: bold; font-family: monospace, sans-serif;">
                                            <?php if ($item['qtd_cabos_fase'] > 1): ?>
                                                <?= esc($item['qtd_cabos_fase']) ?>x{<?= esc($item['categoria']) ?>#<?= esc($item['aereo_fase']) ?>(<?= esc($item['aereo_neutro']) ?>)}[<?= esc($item['aereo_terra']) ?>]
                                            <?php else: ?>
                                                <?= esc($item['categoria']) ?>#<?= esc($item['aereo_fase']) ?>(<?= esc($item['aereo_neutro']) ?>)[<?= esc($item['aereo_terra']) ?>]
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: #bdc3c7; font-size: 0.85rem;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($item['sub_fase']): ?>
                                        <div style="font-size: 0.95rem; color: #d35400; font-weight: bold; font-family: monospace, sans-serif;">
                                            <?php if ($item['qtd_cabos_fase'] > 1): ?>
                                                <?= esc($item['qtd_cabos_fase']) ?>x{<?= esc($item['categoria']) ?>#<?= esc($item['sub_fase']) ?>(<?= esc($item['sub_neutro']) ?>)}[<?= esc($item['sub_terra']) ?>]
                                            <?php else: ?>
                                                <?= esc($item['categoria']) ?>#<?= esc($item['sub_fase']) ?>(<?= esc($item['sub_neutro']) ?>)[<?= esc($item['sub_terra']) ?>]
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: #bdc3c7; font-size: 0.85rem;">-</span>
                                    <?php endif; ?>
                                </td>
                                <!-- <td><?= esc($item['eletroduto']) ?>"</td> -->
                                <td style="text-align: center;">
                                    <a href="<?= base_url('admin/dimensionamento/editar/' . $item['id']) ?>" class="btn-action btn-outline" style="padding: 4px 8px; font-size: 0.85rem; margin-right: 5px;" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= base_url('admin/dimensionamento/excluir/' . $item['id']) ?>" class="btn-action btn-outline" style="padding: 4px 8px; font-size: 0.85rem; color: #e74c3c; border-color: #f5b7b1;" title="Excluir" onclick="return confirm('Deseja realmente excluir este padrão?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #95a5a6; font-style: italic;">
                                Nenhum padrão de dimensionamento cadastrado.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?= $this->endSection() ?>