<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Materiais - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Materiais Cadastrados</h1>
            <div class="space-x-2">
                <a href="<?= base_url('admin/kits') ?>" class="text-blue-600 hover:underline text-sm mr-4">Gerenciar Kits</a>
                <a href="<?= base_url('admin/materiais/novo') ?>" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition">
                    + Novo Material
                </a>
                <a href="<?= base_url('dashboard') ?>" class="text-gray-500 hover:text-gray-700 text-sm ml-4">Voltar Dashboard</a>
            </div>
        </div>

        <?php if(session()->getFlashdata('sucesso')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= session()->getFlashdata('sucesso') ?>
            </div>
        <?php endif; ?>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="border p-3 text-left w-20 text-gray-600">ID</th>
                        <th class="border p-3 text-left text-gray-600">Descrição</th>
                        <th class="border p-3 text-center w-24 text-gray-600">Unidade</th>
                        <th class="border p-3 text-center w-40 text-gray-600">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($materiais)): ?>
                        <tr><td colspan="4" class="p-8 text-center text-gray-500 italic">Nenhum material cadastrado ainda.</td></tr>
                    <?php else: ?>
                        <?php foreach($materiais as $m): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="border p-3 text-gray-500 font-mono text-sm"><?= $m['id'] ?></td>
                            <td class="border p-3 font-medium text-gray-800"><?= esc($m['descricao']) ?></td>
                            <td class="border p-3 text-center text-sm bg-gray-50 text-gray-600"><?= esc($m['unidade']) ?></td>
                            <td class="border p-3 text-center space-x-2">
                                <a href="<?= base_url('admin/materiais/editar/'.$m['id']) ?>" class="bg-blue-100 text-blue-700 px-3 py-1 rounded text-xs font-bold hover:bg-blue-200">Editar</a>
                                <a href="<?= base_url('admin/materiais/excluir/'.$m['id']) ?>" onclick="return confirm('Tem certeza?')" class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs font-bold hover:bg-red-200">Excluir</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>