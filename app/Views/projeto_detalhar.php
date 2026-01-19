<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhar Medições - KvaPro</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
          theme: { extend: { colors: { eletblue: { DEFAULT: '#0f2649', dark: '#0a1a33', light: '#eef2ff' } }, fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans text-eletgray min-h-screen pb-10">

    <nav class="bg-eletblue text-white p-4 shadow-md sticky top-0 z-50">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="<?= base_url('assets/img/logo.png') ?>" alt="KvaPro" class="h-10 bg-white rounded p-1">
                <span class="font-bold text-xl tracking-tight">Passo 6: Detalhar Unidades</span>
            </div>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto mt-8 px-4">
        
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-sm font-bold text-gray-500 uppercase">Projeto</h2>
                <p class="text-lg font-bold text-eletblue"><?= esc($projeto['titulo_obra']) ?></p>
            </div>
            <div class="text-right">
                <h2 class="text-sm font-bold text-gray-500 uppercase">Cliente</h2>
                <p class="text-lg font-bold text-gray-700"><?= esc($projeto['cliente_nome']) ?></p>
            </div>
        </div>

        <form action="<?= base_url('projeto/salvar') ?>" method="POST">
            <?= csrf_field() ?>

            <?php foreach ($projeto as $chave => $valor): ?>
                <?php if (!is_array($valor)): // Campos simples ?>
                    <input type="hidden" name="<?= $chave ?>" value="<?= esc($valor) ?>">
                <?php endif; ?>
            <?php endforeach; ?>

            <div class="space-y-4">
                <?php foreach ($unidades as $i => $u): ?>
                    
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col md:flex-row">
                        
                        <div class="bg-gray-50 p-4 w-full md:w-1/4 border-b md:border-b-0 md:border-r border-gray-100 flex flex-col justify-center">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="bg-eletblue text-white text-xs font-bold px-2 py-0.5 rounded-full">UC #<?= $i + 1 ?></span>
                                <span class="text-xs font-semibold text-gray-500 uppercase"><?= $u['classe'] ?></span>
                            </div>
                            <p class="text-sm font-bold text-gray-800"><?= $u['info_tecnica'] ?></p>
                            
                            <input type="hidden" name="medicoes[<?= $i ?>][classe]" value="<?= $u['classe'] ?>">
                            <input type="hidden" name="medicoes[<?= $i ?>][categoria]" value="<?= $u['categoria'] ?>">
                            <input type="hidden" name="medicoes[<?= $i ?>][dimensionamento_id]" value="<?= $u['dimensionamento_id'] ?>">
                            <input type="hidden" name="medicoes[<?= $i ?>][info_tecnica]" value="<?= $u['info_tecnica'] ?>">
                        </div>

                        <div class="p-4 w-full md:w-3/4 grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                            
                            <div class="md:col-span-1">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Placa de Identificação</label>
                                <input type="text" name="medicoes[<?= $i ?>][identificacao]" 
                                       value="<?= esc($u['nome_sugerido']) ?>" required
                                       class="w-full border-gray-300 rounded focus:ring-eletblue focus:border-eletblue p-2 text-sm border font-semibold text-eletblue"
                                       placeholder="Ex: Casa 1, Ap 101">
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Nº da UC (Instalação)</label>
                                <input type="text" name="medicoes[<?= $i ?>][numero_uc]" 
                                       class="w-full border-gray-300 rounded focus:ring-eletblue focus:border-eletblue p-2 text-sm border"
                                       placeholder="Se já existir">
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Observações</label>
                                <input type="text" name="medicoes[<?= $i ?>][observacao]" 
                                       class="w-full border-gray-300 rounded focus:ring-eletblue focus:border-eletblue p-2 text-sm border"
                                       placeholder="Detalhes extras...">
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="flex justify-between items-center mt-8 mb-12">
                <button type="button" onclick="history.back()" class="text-gray-500 hover:text-eletblue font-semibold flex items-center gap-2 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
                    Voltar e Editar Quantidades
                </button>

                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition duration-200 flex items-center gap-2 transform hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Finalizar e Baixar Projeto
                </button>
            </div>

        </form>
    </div>

</body>
</html>