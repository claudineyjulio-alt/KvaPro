<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EletCAD - Painel</title>
    
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>" />
    
    <!-- Bibliotecas -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        slate: { 850: '#151e2e', 900: '#0f172a', 950: '#020617' },
                        eletred: { DEFAULT: '#D62828', dark: '#A81818' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-950 text-slate-300 h-screen flex flex-col overflow-hidden">

    <!-- HEADER (Simplificado para o Dashboard) -->
    <header class="h-14 bg-slate-900 border-b border-slate-800 flex items-center justify-between px-6 shrink-0 z-30">
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-3">
                <img src="<?= base_url('assets/img/logo.png') ?>" class="h-8 w-auto">
                <span class="font-bold text-lg text-white tracking-wide">EletCAD</span>
            </div>
            
            <!-- Navegação Principal -->
            <nav class="hidden md:flex gap-1 ml-6">
                <a href="#" class="px-3 py-2 text-sm bg-slate-800 text-white rounded-md font-medium transition-colors">Projetos</a>
                <a href="<?= base_url('admin/materiais') ?>" class="px-3 py-2 text-sm hover:bg-slate-800 text-slate-400 hover:text-white rounded-md transition-colors">Biblioteca</a>
                <a href="#" class="px-3 py-2 text-sm hover:bg-slate-800 text-slate-400 hover:text-white rounded-md transition-colors">Clientes</a>
            </nav>
        </div>

        <div class="flex items-center gap-4">
             <!-- Perfil Dropdown (Simulado) -->
             <div class="flex items-center gap-3 pl-4 border-l border-slate-800">
                 <div class="text-right hidden sm:block">
                     <div class="text-sm font-medium text-white"><?= session()->get('nome') ?></div>
                     <div class="text-[10px] text-slate-500 uppercase"><?= session()->get('nivel') ?></div>
                 </div>
                 <img src="<?= session()->get('foto') ?? 'https://ui-avatars.com/api/?name='.session()->get('nome') ?>" class="h-9 w-9 rounded-full border border-slate-700">
                 <a href="<?= base_url('logout') ?>" class="text-slate-500 hover:text-red-400 transition-colors" title="Sair">
                     <i data-lucide="log-out" class="w-5 h-5"></i>
                 </a>
             </div>
        </div>
    </header>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="flex-1 overflow-y-auto bg-slate-950 p-6 md:p-10">
        <?= $this->renderSection('content') ?>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>