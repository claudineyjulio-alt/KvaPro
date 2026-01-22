<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= isset($item) ? 'Editar' : 'Novo' ?> Material</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-lg bg-white p-8 rounded-lg shadow-lg border border-gray-200">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-4">
            <?= isset($item) ? 'Editar Material' : 'Cadastrar Novo Material' ?>
        </h2>

        <form method="post">
            
            <?= csrf_field() ?>
            
            <?php if(isset($item['id'])): ?>
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
            <?php endif; ?>
                      
            <div class="mb-5">
                <label class="block text-gray-700 text-sm font-bold mb-2">Descrição do Material</label>
                <textarea name="descricao" rows="3" class="w-full border border-gray-300 p-3 rounded" required><?= isset($item) ? esc($item['descricao']) : '' ?></textarea>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Unidade</label>
                <select name="unidade" class="w-full border border-gray-300 p-3 rounded">
                    <?php 
                        $unidades = ['Unid', 'm', 'Peça', 'Conjunto', 'Barra', 'Kg', 'L', 'Par'];
                        $atual = $item['unidade'] ?? 'Unid';
                    ?>
                    <?php foreach($unidades as $u): ?>
                        <option value="<?= $u ?>" <?= $atual == $u ? 'selected' : '' ?>><?= $u ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex justify-between items-center pt-4 border-t">
                <a href="<?= base_url('admin/materiais') ?>" class="text-gray-500 font-bold">Cancelar</a>
                <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-8 rounded shadow-lg">
                    Salvar Material
                </button>
            </div>

        </form>
    </div>

</body>
</html>