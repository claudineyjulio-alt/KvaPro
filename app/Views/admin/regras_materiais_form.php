<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= isset($item) ? 'Editar' : 'Nova' ?> Regra</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-4xl bg-white p-8 rounded-lg shadow-lg border border-gray-200">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-4">
            <?= isset($item) ? 'Editar Regra de Decisão' : 'Criar Nova Regra de Decisão' ?>
        </h2>

        <form method="post">
            
            <?= csrf_field() ?>
            <?php if(isset($item['id'])): ?>
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
            <?php endif; ?>
            
            <input type="hidden" name="concessionaria_id" value="1">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Categoria (Tipo)</label>
                    <select name="tipo_kit" class="w-full border border-gray-300 p-3 rounded bg-gray-50" required>
                        <?php $tipos = ['infra' => 'Infraestrutura', 'aterramento' => 'Aterramento', 'medicao' => 'Medição', 'entrada' => 'Entrada']; ?>
                        <?php foreach($tipos as $val => $label): ?>
                            <option value="<?= $val ?>" <?= (isset($item) && $item['tipo_kit'] == $val) ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Prioridade</label>
                    <input type="number" name="prioridade" value="<?= $item['prioridade'] ?? '99' ?>" class="w-full border border-gray-300 p-3 rounded" required>
                    <p class="text-xs text-gray-500 mt-1">1 = Máxima (Exceção), 99 = Mínima (Padrão)</p>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Descrição (Para Humanos)</label>
                    <input type="text" name="descricao" value="<?= $item['descricao'] ?? '' ?>" class="w-full border border-gray-300 p-3 rounded" placeholder="Ex: Poste de 7m se for Rua" required>
                </div>
            </div>

            <div class="bg-blue-50 p-6 rounded-lg border border-blue-200 mb-6">
                <h3 class="text-blue-800 font-bold mb-4 flex items-center gap-2">
                    <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">SE</span>
                    Condição (Input do Projeto)
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-1">
                        <label class="block text-gray-600 text-xs font-bold mb-1">Variável do Projeto</label>
                        <select name="variavel" class="w-full border border-gray-300 p-2 rounded">
                            <option value="padrao" <?= (isset($item) && $item['variavel'] == 'padrao') ? 'selected' : '' ?>>padrao (Regra Geral)</option>
                            <option value="travessia" <?= (isset($item) && $item['variavel'] == 'travessia') ? 'selected' : '' ?>>travessia</option>
                            <option value="fases" <?= (isset($item) && $item['variavel'] == 'fases') ? 'selected' : '' ?>>fases (nº)</option>
                            <option value="terra_hastes" <?= (isset($item) && $item['variavel'] == 'terra_hastes') ? 'selected' : '' ?>>terra_hastes</option>
                            <option value="entrada_cabo" <?= (isset($item) && $item['variavel'] == 'entrada_cabo') ? 'selected' : '' ?>>entrada_cabo</option>
                        </select>
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-gray-600 text-xs font-bold mb-1">Condição</label>
                        <select name="condicao" class="w-full border border-gray-300 p-2 rounded text-center font-mono">
                            <option value="=" <?= (isset($item) && $item['condicao'] == '=') ? 'selected' : '' ?>>IGUAL (=)</option>
                            <option value="CONTEM" <?= (isset($item) && $item['condicao'] == 'CONTEM') ? 'selected' : '' ?>>CONTÉM (Texto)</option>
                            <option value=">" <?= (isset($item) && $item['condicao'] == '>') ? 'selected' : '' ?>>MAIOR QUE (>)</option>
                            <option value=">=" <?= (isset($item) && $item['condicao'] == '>=') ? 'selected' : '' ?>>MAIOR OU IGUAL (>=)</option>
                        </select>
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-gray-600 text-xs font-bold mb-1">Valor</label>
                        <input type="text" name="valor_min" value="<?= $item['valor_min'] ?? '' ?>" class="w-full border border-gray-300 p-2 rounded" placeholder="Ex: rua, 3, true">
                    </div>
                    
                    <div class="md:col-span-1">
                        <label class="block text-gray-600 text-xs font-bold mb-1">Valor Máx (Se Between)</label>
                        <input type="text" name="valor_max" value="<?= $item['valor_max'] ?? '' ?>" class="w-full border border-gray-300 p-2 rounded">
                    </div>
                </div>
            </div>

            <div class="bg-green-50 p-6 rounded-lg border border-green-200 mb-6">
                <h3 class="text-green-800 font-bold mb-4 flex items-center gap-2">
                    <span class="bg-green-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">ENTÃO</span>
                    Resultado (Kit a Usar)
                </h3>
                <div>
                    <label class="block text-gray-600 text-xs font-bold mb-1">Selecione o Kit</label>
                    <select name="kit_id" class="w-full border border-green-300 p-3 rounded font-bold text-gray-700" required>
                        <option value="">Selecione um Kit...</option>
                        <?php if(isset($kits)): ?>
                            <?php foreach($kits as $k): ?>
                                <option value="<?= $k['id'] ?>" <?= (isset($item) && $item['kit_id'] == $k['id']) ? 'selected' : '' ?>>
                                    [<?= $k['slug'] ?>] <?= $k['nome'] ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="flex justify-between items-center pt-4 border-t">
                <a href="<?= base_url('admin/regras-materiais') ?>" class="text-gray-500 font-bold">Cancelar</a>
                <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-8 rounded shadow-lg hover:bg-blue-700 transition">
                    Salvar Regra
                </button>
            </div>

        </form>
    </div>

</body>
</html>