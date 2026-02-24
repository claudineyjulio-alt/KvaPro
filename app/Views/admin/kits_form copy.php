<!-- app/Views/admin/kits_form.php -->
<?= $this->extend('layouts/kvapro_layout') ?>

<?= $this->section('title') ?><?= isset($kit) ? 'Editar' : 'Novo' ?> Kit<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-body">

    <div class="page-actions">
        <a href="<?= base_url('admin/kits') ?>" class="btn-action btn-outline">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <form method="post" action="<?= current_url() ?>">
        <?= csrf_field() ?>

        <?php if (isset($kit['id'])): ?>
            <input type="hidden" name="id" value="<?= $kit['id'] ?>">
        <?php endif; ?>

        <div class="form-card">
            <div class="form-card-header">
                <h2 class="step-title">
                    <i class="fas fa-cubes" style="color: var(--navy-accent); margin-right: 8px;"></i>
                    <?= isset($kit) ? 'Editar Kit de Materiais' : 'Criar Novo Kit' ?>
                </h2>
            </div>

            <div class="form-card-body">

                <div class="section-subtitle">Identificação do Kit</div>
                <div class="form-grid" style="margin-bottom: 30px;">
                    <div class="col-8">
                        <label class="form-label">Nome do Kit</label>
                        <input type="text" name="nome" value="<?= isset($kit) ? esc($kit['nome']) : '' ?>"
                            class="form-control" required placeholder="Ex: Aterramento Padrão 3 Hastes">
                    </div>
                    <div class="col-4">
                        <label class="form-label">Slug (Código Interno) <span style="font-weight: normal; color: #999; font-size: 0.75rem;">(Sem espaços)</span></label>
                        <input type="text" name="slug" value="<?= isset($kit) ? esc($kit['slug']) : '' ?>"
                            class="form-control" style="background-color: #f8f9fa; font-family: monospace;"
                            required placeholder="Ex: terra_3_hastes">
                    </div>
                </div>

                <div class="section-subtitle" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>Composição de Materiais Base</span>
                    <button type="button" onclick="adicionarLinha()" class="btn-action btn-outline" style="font-size: 0.75rem; padding: 4px 10px; border-color: #27ae60; color: #27ae60;">
                        <i class="fas fa-plus"></i> Adicionar Material
                    </button>
                </div>

                <div style="border: 1px solid #eee; border-radius: 6px; overflow: hidden; margin-bottom: 30px;">
                    <div class="table-responsive">
                        <table class="kv-table" style="margin: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Material Base (Descrição e Unidade)</th>
                                    <th style="width: 150px; text-align: center;">Quantidade</th>
                                    <th style="width: 80px; text-align: center;">Remover</th>
                                </tr>
                            </thead>
                            <tbody id="lista-itens">
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="text-align: right; border-top: 1px solid #eee; padding-top: 20px;">
                    <a href="<?= base_url('admin/kits') ?>" class="btn-action btn-outline" style="margin-right: 10px;">
                        Cancelar
                    </a>
                    <button type="submit" class="btn-action btn-primary" style="padding: 10px 30px; font-size: 1rem;">
                        <i class="fas fa-save"></i> <?= isset($kit) ? 'Atualizar Kit' : 'Salvar Kit' ?>
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>

<template id="tpl-linha">
    <tr>
        <td style="padding: 8px 15px;">
            <select name="materiais[]" class="form-control" style="border-color: #e0e0e0; background-color: #fcfcfc;" required>
                <option value="">Selecione um material...</option>
                <?php if (isset($todosMateriais)): ?>
                    <?php foreach ($todosMateriais as $m): ?>
                        <option value="<?= $m['id'] ?>">
                            <?= esc($m['descricao']) ?> (<?= $m['unidade'] ?>)
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </td>
        <td style="padding: 8px 15px;">
            <input type="number" step="0.01" name="qtds[]" class="form-control" style="text-align: center; border-color: #e0e0e0;" value="1" required>
        </td>
        <td style="text-align: center; padding: 8px 15px;">
            <button type="button" onclick="removerLinha(this)" title="Remover Material" style="background: none; border: none; color: #e74c3c; cursor: pointer; padding: 5px; font-size: 1.1rem; transition: color 0.2s;">
                <i class="fas fa-times"></i>
            </button>
        </td>
    </tr>
</template>

<script>
    // Carrega dados se for edição
    const itensSalvos = <?= isset($itensKit) ? json_encode($itensKit) : '[]' ?>;

    document.addEventListener('DOMContentLoaded', () => {
        if (itensSalvos.length > 0) {
            itensSalvos.forEach(item => adicionarLinha(item));
        } else {
            adicionarLinha(); // Adiciona linha vazia para começar se for novo
        }
    });

    function adicionarLinha(dados = null) {
        const tpl = document.getElementById('tpl-linha');
        const clone = tpl.content.cloneNode(true);
        const tbody = document.getElementById('lista-itens');

        if (dados) {
            // Usa querySelector para encontrar o select e o input da nova linha clonada
            clone.querySelector('select').value = dados.material_id;
            clone.querySelector('input').value = parseFloat(dados.quantidade);
        }

        tbody.appendChild(clone);
    }

    function removerLinha(btn) {
        // Encontra a linha <tr> mais próxima e remove
        btn.closest('tr').remove();
    }
</script>

<?= $this->endSection() ?>