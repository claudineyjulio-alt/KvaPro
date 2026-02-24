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

    <form method="post" action="<?= current_url() ?>">
        <?= csrf_field() ?>

        <?php if (isset($item['id'])): ?>
            <input type="hidden" name="id" value="<?= $item['id'] ?>">
        <?php endif; ?>

        <div class="form-card">
            <div class="form-card-header">
                <h2 class="step-title">
                    <i class="fas fa-brain" style="color: var(--navy-accent); margin-right: 8px;"></i>
                    <?= isset($item) ? 'Editar Regra de Automação' : 'Criar Nova Regra de Automação' ?>
                </h2>
            </div>

            <div class="form-card-body">

                <div class="section-subtitle">Configurações Gerais</div>
                <div class="form-grid" style="margin-bottom: 30px;">
                    <div class="col-4">
                        <label class="form-label">Nome da Regra (Identificação Curta)</label>
                        <input type="text" name="nome" value="<?= $item['nome'] ?? '' ?>" class="form-control" placeholder="Ex: Aterramento 3 Hastes" required>
                    </div>

                    <div class="col-3">
                        <label class="form-label">Categoria (Tipo)</label>
                        <select name="tipo_kit" class="form-control" style="background-color: #f8f9fa;" required>
                            <?php $tipos = ['infra' => 'Infraestrutura', 'aterramento' => 'Aterramento', 'medicao' => 'Medição', 'entrada' => 'Entrada']; ?>
                            <?php foreach ($tipos as $val => $label): ?>
                                <option value="<?= $val ?>" <?= (isset($item) && $item['tipo_kit'] == $val) ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-2">
                        <label class="form-label">Prioridade <span style="font-weight: normal; color: #999;">(1 a 99)</span></label>
                        <input type="number" name="prioridade" value="<?= $item['prioridade'] ?? '99' ?>" class="form-control" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Descrição / Motivo (Para Auditoria)</label>
                        <input type="text" name="descricao" value="<?= $item['descricao'] ?? '' ?>" class="form-control" placeholder="Ex: Aplica o kit de 3 hastes se o projeto exigir e a caixa for de PVC" required>
                    </div>
                </div>

                <div style="background-color: #f0f8ff; border: 1px solid #cce5ff; border-radius: 8px; padding: 20px; margin-bottom: 30px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h3 style="color: #004085; font-size: 0.95rem; font-weight: bold; display: flex; align-items: center; gap: 8px; margin: 0;">
                            <span style="background: #007bff; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem;">SE (Condições)</span>
                            O projeto atender a TODAS as condições abaixo:
                        </h3>
                        <button type="button" onclick="adicionarCondicao()" class="btn-action btn-outline" style="font-size: 0.75rem; padding: 4px 10px; border-color: #007bff; color: #007bff;">
                            <i class="fas fa-plus"></i> Adicionar Regra (E)
                        </button>
                    </div>

                    <div class="form-grid" style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px solid #cce5ff;">
                        <div class="col-4"><label class="form-label" style="margin:0; color: #004085;">Variável do Projeto</label></div>
                        <div class="col-3"><label class="form-label" style="margin:0; color: #004085;">Lógica</label></div>
                        <div class="col-3"><label class="form-label" style="margin:0; color: #004085;">Valor Alvo</label></div>
                        <div class="col-2"><label class="form-label" style="margin:0; color: #004085;">Máx / Ação</label></div>
                    </div>

                    <div id="lista-condicoes"></div>

                </div>

                <div style="background-color: #f8fff9; border: 1px solid #d4edda; border-radius: 8px; padding: 20px; margin-bottom: 30px;">
                    <h3 style="color: #155724; font-size: 0.95rem; font-weight: bold; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                        <span style="background: #28a745; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem;">ENTÃO</span>
                        Aplique este Kit de Materiais:
                    </h3>

                    <div class="form-grid">
                        <div class="col-12">
                            <select name="kit_id" class="form-control" style="font-weight: bold; border-color: #28a745; background-color: white; font-size: 1rem;" required>
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

<template id="tpl-condicao">
    <div class="form-grid condicao-row" style="margin-bottom: 10px; align-items: center; background: white; padding: 10px; border-radius: 6px; border: 1px solid #e1e8ed;">

        <div class="col-4">
            <select name="variaveis[]" class="form-control inp-variavel" required>
                <option value="">Selecione...</option>
                <?php if (isset($variaveisProjeto)): ?>
                    <?php foreach ($variaveisProjeto as $grupo => $variaveis): ?>
                        <optgroup label="--- <?= mb_strtoupper($grupo) ?> ---">
                            <?php foreach ($variaveis as $chave => $label): ?>
                                <option value="<?= $chave ?>"><?= $chave ?> (<?= $label ?>)</option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="col-3">
            <select name="condicoes[]" class="form-control inp-condicao" style="font-family: monospace;" required>
                <option value="=">IGUAL (=)</option>
                <option value="!=">DIFERENTE (!=)</option>
                <option value="CONTEM">CONTÉM (Texto)</option>
                <option value=">">MAIOR QUE (>)</option>
                <option value="<">MENOR QUE (<)< /option>
                <option value=">=">MAIOR OU IGUAL (>=)</option>
                <option value="<=">MENOR OU IGUAL (<=)< /option>
            </select>
        </div>

        <div class="col-3">
            <input type="text" name="valores_min[]" class="form-control inp-min" placeholder="Ex: 3, rua, Aereo" required>
        </div>

        <div class="col-2" style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="valores_max[]" class="form-control inp-max" placeholder="Opcional">

            <button type="button" onclick="removerCondicao(this)" title="Remover Condição" style="background: none; border: none; color: #e74c3c; cursor: pointer; padding: 5px; font-size: 1.1rem; transition: 0.2s;">
                <i class="fas fa-times"></i>
            </button>
        </div>

    </div>
</template>

<script>
    // Se estiver editando, o Controller deve passar a variável $condicoesRegra com as linhas do banco
    const condicoesSalvas = <?= isset($condicoesRegra) ? json_encode($condicoesRegra) : '[]' ?>;

    document.addEventListener('DOMContentLoaded', () => {
        if (condicoesSalvas.length > 0) {
            // Se tem condições salvas, recria as linhas
            condicoesSalvas.forEach(cond => adicionarCondicao(cond));
        } else {
            // Se for uma regra nova, já inicia com 1 linha vazia na tela
            adicionarCondicao();
        }
    });

    function adicionarCondicao(dados = null) {
        const tpl = document.getElementById('tpl-condicao');
        const clone = tpl.content.cloneNode(true);
        const container = document.getElementById('lista-condicoes');

        // Se for edição, preenche os campos clonados com os valores do banco
        if (dados) {
            clone.querySelector('.inp-variavel').value = dados.variavel;
            clone.querySelector('.inp-condicao').value = dados.condicao;
            clone.querySelector('.inp-min').value = dados.valor_min;
            clone.querySelector('.inp-max').value = dados.valor_max || '';
        }

        container.appendChild(clone);
    }

    function removerCondicao(btn) {
        const row = btn.closest('.condicao-row');
        // Impede de apagar se for a última linha (a regra precisa ter pelo menos 1 condição)
        const totalLinhas = document.querySelectorAll('.condicao-row').length;
        if (totalLinhas > 1) {
            row.remove();
        } else {
            alert('A regra precisa ter pelo menos uma condição.');
        }
    }
</script>

<?= $this->endSection() ?>