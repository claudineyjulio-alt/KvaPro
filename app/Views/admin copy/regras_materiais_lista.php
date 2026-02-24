<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Regras de Materiais</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">

<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Motor de Regras (Materiais)</h1>
        <a href="<?= base_url('admin/regras-materiais/novo') ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
            + Nova Regra
        </a>
    </div>

    <?php if(session()->getFlashdata('sucesso')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= session()->getFlashdata('sucesso') ?>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Prio.</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Categoria</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Condição (Se...)</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Resultado (Então Use...)</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($regras)): ?>
                    <?php foreach($regras as $r): ?>
                        <tr class="hover:bg-gray-50 border-b border-gray-200">
                            <td class="px-5 py-5 text-sm">
                                <span class="px-2 py-1 font-bold rounded <?= $r['prioridade'] == 1 ? 'bg-red-100 text-red-800' : ($r['prioridade'] == 99 ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800') ?>">
                                    <?= $r['prioridade'] ?>
                                </span>
                            </td>

                            <td class="px-5 py-5 text-sm font-bold text-gray-700">
                                <?= strtoupper($r['tipo_kit']) ?>
                            </td>

                            <td class="px-5 py-5 text-sm">
                                <div class="font-bold text-gray-800"><?= $r['descricao'] ?></div>
                                <code class="text-xs bg-gray-100 p-1 rounded text-blue-600">
                                    Se [<?= $r['variavel'] ?>] <?= $r['condicao'] ?> <?= $r['valor_min'] ?> <?= $r['valor_max'] ? 'e '.$r['valor_max'] : '' ?>
                                </code>
                            </td>

                            <td class="px-5 py-5 text-sm">
                                <span class="flex items-center gap-2 text-green-700 font-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                    <?= $kitsMap[$r['kit_id']] ?? 'Kit ID: '.$r['kit_id'] ?>
                                </span>
                            </td>

                            <td class="px-5 py-5 text-sm">
                                <a href="<?= base_url('admin/regras-materiais/editar/'.$r['id']) ?>" class="text-blue-600 hover:text-blue-900 mr-3">Editar</a>
                                <a href="<?= base_url('admin/regras-materiais/excluir/'.$r['id']) ?>" onclick="return confirm('Tem certeza?')" class="text-red-600 hover:text-red-900">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-5 py-5 text-center text-gray-500">Nenhuma regra cadastrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        <a href="<?= base_url('admin/dashboard') ?>" class="text-gray-500 hover:text-gray-700">&larr; Voltar ao Dashboard</a>
    </div>
</div>

</body>
</html>