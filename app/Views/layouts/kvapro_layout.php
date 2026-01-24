<!-- app/Views/layouts/kvapro_layout.php -->
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EletCAD - KvaPro</title>

    <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/kvapro.css') ?>">

</head>

<body>
    <div class="main-wrapper" id="mainWrapper">
        <!-- Incluindo Partials -->
        <?= $this->include('partials/kvapro_sidebar') ?>

        <header class="top-header">
            <div style="display: flex; align-items: center; gap: 20px;">
                <button class="toggle-menu" onclick="handleSidebarToggle()"><i class="fas fa-bars"></i></button>
                <h3 style="font-weight: 400; font-size: 1.1rem;">Painel do Projetista</h3>
            </div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="text-align: right; font-size: 0.8rem;" class="hide-mobile">
                    <div style="color: var(--white);">Eng. Claudinei</div>
                </div>
                <div
                    style="width: 35px; height: 35px; background: var(--navy-accent); border-radius: 50%; color: var(--navy-dark); display: flex; align-items: center; justify-content: center; font-weight: bold;">
                    EC</div>
            </div>
        </header>

        <div class="content">
            <!-- Conteúdo específico da página -->
            <?= $this->renderSection('content') ?>
        </div>

        <footer class="main-footer">
            <p>&copy; 2026 Sistema de Projetos Elétricos</p>
        </footer>
    </div>

    <?= view('partials/modal_mensagens'); ?>
    <script src="<?= base_url('assets/js/kvapro.js') ?>"></script>
</body>

</html>