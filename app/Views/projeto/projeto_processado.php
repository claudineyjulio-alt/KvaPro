<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projeto Processado - KvaPro</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        eletblue: {
                            DEFAULT: '#0f2649',
                            dark: '#0a1a33'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-100 font-sans text-gray-800 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-3xl w-full bg-white rounded-2xl shadow-2xl overflow-hidden">

        <div class="bg-green-600 p-8 text-center text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-3 text-green-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h1 class="text-3xl font-bold tracking-tight">Projeto Processado!</h1>
            <p class="text-green-100 mt-2">Todos os cálculos e desenhos foram gerados com sucesso.</p>
        </div>

        <div class="p-8">
            <div class="bg-blue-50 p-5 rounded-xl border border-blue-100 mb-8 flex flex-col md:flex-row justify-between items-center text-center md:text-left gap-4">
                <div>
                    <h3 class="text-xs font-bold text-blue-500 uppercase tracking-wider mb-1">Obra</h3>
                    <p class="font-bold text-gray-800 text-lg"><?= esc($dados['titulo_obra']) ?></p>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-blue-500 uppercase tracking-wider mb-1">Cliente</h3>
                    <p class="font-bold text-gray-800 text-lg"><?= esc($dados['cliente_nome']) ?></p>
                </div>
                <div class="bg-white px-4 py-2 rounded-lg border border-blue-100 shadow-sm">
                    <span class="block text-2xl font-bold text-eletblue"><?= isset($dados['unidades']) ? count($dados['unidades']) : 0 ?></span>
                    <span class="text-xs text-gray-500 font-semibold uppercase">Medições</span>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-sm font-bold text-gray-400 uppercase mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Downloads e Documentos
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <button onclick="baixarArquivoKva()" class="md:col-span-2 bg-eletblue hover:bg-blue-900 text-white p-4 rounded-xl shadow-md flex items-center justify-center gap-3 transition transform hover:-translate-y-1 group">
                        <div class="bg-white/20 p-2 rounded-lg group-hover:bg-white/30 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0l-4 4m4-4v12" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <span class="block font-bold text-lg">Baixar Arquivo .KVA</span>
                            <span class="text-xs text-blue-200">Essencial para importar/editar depois</span>
                        </div>
                    </button>

                    <form action="<?= base_url('projeto/diagrama') ?>" method="post" target="_blank" class="w-full">
                        <input type="hidden" name="payload_projeto" value="<?= htmlspecialchars(json_encode($dados)) ?>">
                        <button type="submit" class="w-full bg-white border-2 border-indigo-100 hover:border-indigo-500 hover:bg-indigo-50 text-indigo-700 p-4 rounded-xl flex items-center gap-3 transition group h-full">
                            <div class="bg-indigo-100 text-indigo-600 p-2 rounded-lg group-hover:bg-indigo-500 group-hover:text-white transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <span class="block font-bold">Ver Unifilar</span>
                                <span class="text-xs text-gray-500">Diagrama técnico SVG</span>
                            </div>
                        </button>
                    </form>

                    <form action="<?= base_url('projeto/lista-materiais') ?>" method="post" target="_blank" class="w-full">
                        <input type="hidden" name="payload_projeto" value="<?= htmlspecialchars(json_encode($dados)) ?>">
                        <button type="submit" class="w-full bg-white border-2 border-emerald-100 hover:border-emerald-500 hover:bg-emerald-50 text-emerald-700 p-4 rounded-xl flex items-center gap-3 transition group h-full">
                            <div class="bg-emerald-100 text-emerald-600 p-2 rounded-lg group-hover:bg-emerald-500 group-hover:text-white transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <span class="block font-bold">Lista Materiais</span>
                                <span class="text-xs text-gray-500">Quantitativo estimado</span>
                            </div>
                        </button>
                    </form>
                    <form action="<?= base_url('projeto/layout') ?>" method="post" target="_blank" class="w-full">
                        <input type="hidden" name="payload_projeto" value="<?= htmlspecialchars(json_encode($dados)) ?>">
                        <button type="submit" class="w-full bg-white border-2 border-emerald-100 hover:border-emerald-500 hover:bg-emerald-50 text-emerald-700 p-4 rounded-xl flex items-center gap-3 transition group h-full">
                            <div class="bg-emerald-100 text-emerald-600 p-2 rounded-lg group-hover:bg-emerald-500 group-hover:text-white transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <span class="block font-bold">Layout</span>
                                <span class="text-xs text-gray-500">Desenho da medição</span>
                            </div>
                        </button>
                    </form>

                </div>
            </div>

            <hr class="border-gray-100 my-6">

            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <form action="<?= base_url('projeto/novo') ?>" method="post" class="w-full sm:w-auto">
                    <input type="hidden" name="recuperar_projeto" value="<?= htmlspecialchars(json_encode($dados)) ?>">
                    <button type="submit" class="w-full text-gray-500 hover:text-eletblue font-semibold py-2 px-4 rounded-lg hover:bg-gray-100 transition flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Voltar e Corrigir
                    </button>
                </form>

                <a href="<?= base_url('projeto/novo') ?>" class="w-full sm:w-auto bg-gray-800 hover:bg-black text-white font-bold py-3 px-6 rounded-lg shadow transition flex items-center justify-center gap-2">
                    Iniciar Novo Projeto
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </a>
            </div>

        </div>
    </div>

    <script>
        function baixarArquivoKva() {
            const dados = <?= json_encode($dados) ?>;
            const nomeObra = dados.titulo_obra ? dados.titulo_obra.replace(/[^a-z0-9]/gi, '_').toLowerCase() : 'projeto';
            const nomeArquivo = nomeObra + '.kva';

            const jsonStr = JSON.stringify(dados, null, 2);
            const blob = new Blob([jsonStr], {
                type: "application/json"
            });
            const url = URL.createObjectURL(blob);

            const a = document.createElement('a');
            a.href = url;
            a.download = nomeArquivo;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
    </script>
</body>

</html>