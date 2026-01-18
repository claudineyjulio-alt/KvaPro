<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EletCAD - Editor de Engenharia</title>
    
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>" />
    
    <!-- Bibliotecas de Estilo e Ícones -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/konva@9.2.0/konva.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Configurações Globais de Estilo -->
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        
        /* Scrollbars finas */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }

        /* Cursores */
        .cursor-crosshair { cursor: crosshair !important; }
        .cursor-grab { cursor: grab; }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        slate: { 850: '#151e2e', 900: '#0f172a', 950: '#020617' }
                    }
                }
            }
        }
    </script>
    
    <!-- Espaço para CSS específico de cada View -->
    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-slate-950 text-slate-300 h-screen flex flex-col overflow-hidden selection:bg-blue-500/30">

    <!-- 1. Header (Topo) -->
    <?= $this->include('partials/cad_header') ?>

    <!-- 2. Toolbar (Ferramentas) -->
    <?= $this->include('partials/cad_toolbar') ?>

    <!-- 3. Corpo Principal (Flex Row) -->
    <div class="flex-1 flex overflow-hidden">
        
        <!-- Sidebar Esquerda (Biblioteca/Árvore) -->
        <?= $this->include('partials/cad_sidebar_left') ?>

        <!-- Área Central (Canvas) -->
        <main id="workspace" class="flex-1 bg-slate-950 relative overflow-hidden cursor-crosshair">
            <?= $this->renderSection('content') ?>
        </main>

        <!-- Sidebar Direita (Propriedades) -->
        <?= $this->include('partials/cad_sidebar_right') ?>

    </div>

    <!-- Inicialização de Ícones Global -->
    <script>
        lucide.createIcons();
    </script>

    <!-- Espaço para Scripts específicos de cada View -->
    <?= $this->renderSection('scripts') ?>

</body>
</html>