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
          theme: { extend: { colors: { eletblue: { DEFAULT: '#0f2649', dark: '#0a1a33' } }, fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-2xl w-full bg-white rounded-xl shadow-xl overflow-hidden text-center">
        
        <div class="bg-green-500 p-8 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h1 class="text-3xl font-bold">Projeto Processado!</h1>
            <p class="opacity-90 mt-2">Os dados estão prontos para serem salvos.</p>
        </div>

        <div class="p-8 space-y-6">
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 text-left">
                <h3 class="text-sm font-bold text-gray-500 uppercase mb-2">Resumo</h3>
                <p><strong>Obra:</strong> <?= esc($dados['titulo_obra']) ?></p>
                <p><strong>Cliente:</strong> <?= esc($dados['cliente_nome']) ?></p>
                <p><strong>Medições:</strong> <?= isset($dados['medicoes']) ? count($dados['medicoes']) : 0 ?></p>
            </div>

            <p class="text-sm text-gray-600">
                Como este sistema não salva no banco de dados, você deve baixar o arquivo do projeto (<b>.kva</b>) para importá-lo novamente no futuro.
            </p>

            <div class="flex flex-col md:flex-row gap-4 justify-center">
                <button onclick="baixarArquivoKva()" class="bg-eletblue hover:bg-blue-900 text-white font-bold py-3 px-6 rounded-lg shadow-lg flex items-center justify-center gap-2 transition transform hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    Baixar Arquivo .kva
                </button>

                <a href="<?= base_url('projeto/novo') ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded-lg flex items-center justify-center gap-2 transition">
                    Novo Projeto
                </a>
            </div>
        </div>
    </div>

    <script>
        function baixarArquivoKva() {
            // Pega os dados vindos do PHP
            const dados = <?= json_encode($dados) ?>;
            
            // Cria o nome do arquivo limpo (sem caracteres especiais)
            const nomeObra = dados.titulo_obra ? dados.titulo_obra.replace(/[^a-z0-9]/gi, '_').toLowerCase() : 'projeto';
            const nomeArquivo = nomeObra + '.kva';

            // Cria o Blob e o Link
            const jsonStr = JSON.stringify(dados, null, 2);
            const blob = new Blob([jsonStr], { type: "application/json" });
            const url = URL.createObjectURL(blob);
            
            const a = document.createElement('a');
            a.href = url;
            a.download = nomeArquivo;
            document.body.appendChild(a);
            a.click();
            
            // Limpa
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>