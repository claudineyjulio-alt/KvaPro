<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= isset($kit) ? 'Editar' : 'Novo' ?> Kit</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-lg border border-gray-200">
        
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                <?= isset($kit) ? 'Editar Kit de Materiais' : 'Criar Novo Kit' ?>
            </h2>
            <a href="<?= base_url('admin/kits') ?>" class="text-sm text-gray-500 hover:text-gray-800">Cancelar e Voltar</a>
        </div>

        <form method="post">
            
            <?= csrf_field() ?>

            <?php if(isset($kit['id'])): ?>
                <input type="hidden" name="id" value="<?= $kit['id'] ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nome do Kit</label>
                    <input type="text" name="nome" value="<?= isset($kit) ? esc($kit['nome']) : '' ?>" 
                           class="w-full border border-gray-300 p-3 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 transition" 
                           required placeholder="Ex: Aterramento Padrão 3 Hastes">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Slug (Código Interno)</label>
                    <input type="text" name="slug" value="<?= isset($kit) ? esc($kit['slug']) : '' ?>" 
                           class="w-full border border-gray-300 p-3 rounded bg-gray-50 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" 
                           required placeholder="Ex: terra_3_hastes">
                    <p class="text-xs text-gray-400 mt-1">Este código será usado pelo sistema para chamar o kit.</p>
                </div>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" /></svg>
                    Composição do Kit
                </h3>
                
                <table class="w-full mb-4">
                    <thead>
                        <tr class="text-left text-xs font-bold text-gray-500 uppercase border-b border-gray-300">
                            <th class="p-2 w-3/4">Material</th>
                            <th class="p-2 w-1/6 text-center">Qtd.</th>
                            <th class="p-2 text-center">Remover</th>
                        </tr>
                    </thead>
                    <tbody id="lista-itens">
                        </tbody>
                </table>

                <button type="button" onclick="adicionarLinha()" class="flex items-center gap-2 bg-white border border-green-500 text-green-600 px-4 py-2 rounded font-bold hover:bg-green-50 transition shadow-sm text-sm">
                    + Adicionar Material
                </button>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-6 border-t">
                <a href="<?= base_url('admin/kits') ?>" class="px-6 py-3 rounded text-gray-600 font-bold hover:bg-gray-100 transition">Cancelar</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded shadow-lg transition transform hover:-translate-y-0.5">
                    Salvar Kit
                </button>
            </div>
        </form>
    </div>

    <template id="tpl-linha">
        <tr class="border-b border-gray-200 hover:bg-white transition group">
            <td class="p-2">
                <select name="materiais[]" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:border-blue-500 bg-white">
                    <option value="">Selecione...</option>
                    <?php if(isset($todosMateriais)): ?>
                        <?php foreach($todosMateriais as $m): ?>
                            <option value="<?= $m['id'] ?>">
                                <?= esc($m['descricao']) ?> (<?= $m['unidade'] ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </td>
            <td class="p-2">
                <input type="number" step="0.01" name="qtds[]" class="w-full border border-gray-300 p-2 rounded text-center focus:outline-none focus:border-blue-500" value="1">
            </td>
            <td class="p-2 text-center">
                <button type="button" onclick="removerLinha(this)" class="text-red-400 hover:text-red-600 p-2 rounded hover:bg-red-50 transition">
                    X
                </button>
            </td>
        </tr>
    </template>

    <script>
        // Carrega dados se for edição
        const itensSalvos = <?= isset($itensKit) ? json_encode($itensKit) : '[]' ?>;

        document.addEventListener('DOMContentLoaded', () => {
            if(itensSalvos.length > 0) {
                itensSalvos.forEach(item => adicionarLinha(item));
            } else {
                adicionarLinha(); // Adiciona linha vazia para começar
            }
        });

        function adicionarLinha(dados = null) {
            const tpl = document.getElementById('tpl-linha');
            const clone = tpl.content.cloneNode(true);
            const tbody = document.getElementById('lista-itens');

            if(dados) {
                clone.querySelector('select').value = dados.material_id;
                clone.querySelector('input').value = parseFloat(dados.quantidade);
            }

            tbody.appendChild(clone);
        }

        function removerLinha(btn) {
            btn.closest('tr').remove();
        }
    </script>
</body>
</html>