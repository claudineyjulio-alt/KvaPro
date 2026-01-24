<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto">
    
    <!-- Boas Vindas -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Olá, <a href="<?= base_url('perfil') ?>"><?= explode(' ', session()->get('nome'))[0] ?>!</a> 👋</h1>
            <p class="text-slate-400">Bem-vindo ao seu painel de engenharia.</p>
        </div>
        
        <div class="flex gap-3">
            <a href="<?= base_url('projeto/novo') ?>" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-lg font-medium shadow-lg shadow-blue-900/20 transition-all hover:scale-105">
                <i data-lucide="plus-square" class="w-5 h-5"></i>
                Novo Projeto
            </a>
        </div>
    </div>

    <!-- Grid de Cards de Ação Rápida -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <!-- Card 1 -->
        <a href="<?= base_url('diagrama') ?>" class="bg-slate-900 border border-slate-800 p-6 rounded-xl hover:border-blue-500/50 hover:bg-slate-850 transition-all group">
            <div class="w-12 h-12 bg-blue-500/10 text-blue-500 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i data-lucide="pen-tool" class="w-6 h-6"></i>
            </div>
            <h3 class="text-lg font-semibold text-white mb-1">Editor de Diagramas</h3>
            <p class="text-sm text-slate-500">Acesse o CAD para criar ou editar diagramas unifilares e de comando.</p>
        </a>

        <!-- Card 2 -->
        <a href="<?= base_url('simbolos') ?>" class="bg-slate-900 border border-slate-800 p-6 rounded-xl hover:border-purple-500/50 hover:bg-slate-850 transition-all group">
            <div class="w-12 h-12 bg-purple-500/10 text-purple-500 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i data-lucide="component" class="w-6 h-6"></i>
            </div>
            <h3 class="text-lg font-semibold text-white mb-1">Biblioteca de Símbolos</h3>
            <p class="text-sm text-slate-500">Gerencie os componentes, bornes e layouts visuais do sistema.</p>
        </a>

        <!-- Card 3 -->
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-xl hover:border-green-500/50 hover:bg-slate-850 transition-all group cursor-pointer">
            <div class="w-12 h-12 bg-green-500/10 text-green-500 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i data-lucide="clipboard-list" class="w-6 h-6"></i>
            </div>
            <h3 class="text-lg font-semibold text-white mb-1">Listas de Materiais</h3>
            <p class="text-sm text-slate-500">Gere relatórios automáticos de compras baseados nos seus projetos.</p>
        </div>
    </div>

    <!-- Lista de Projetos Recentes -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-800 flex justify-between items-center bg-slate-900/50">
            <h2 class="font-semibold text-white flex items-center gap-2">
                <i data-lucide="clock" class="w-4 h-4 text-slate-500"></i> Recentes
            </h2>
            <button class="text-xs text-blue-400 hover:text-blue-300">Ver todos</button>
        </div>
        
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-slate-500 uppercase bg-slate-950/50">
                <tr>
                    <th class="px-6 py-3">Nome do Projeto</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Última Edição</th>
                    <th class="px-6 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                <?php foreach($projetos_recentes as $proj): ?>
                <tr class="hover:bg-slate-800/50 transition-colors group">
                    <td class="px-6 py-4 font-medium text-white flex items-center gap-3">
                        <div class="w-8 h-8 bg-slate-800 rounded border border-slate-700 flex items-center justify-center">
                            <i data-lucide="file-code" class="w-4 h-4 text-slate-400"></i>
                        </div>
                        <?= $proj['nome'] ?>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-slate-800 text-slate-300 text-xs px-2 py-1 rounded border border-slate-700">
                            <?= $proj['status'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-500"><?= $proj['data'] ?></td>
                    <td class="px-6 py-4 text-right">
                        <a href="<?= base_url('diagrama') ?>" class="text-blue-500 hover:text-blue-400 font-medium text-xs opacity-0 group-hover:opacity-100 transition-opacity">Abrir Editor -></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<?= $this->endSection() ?>