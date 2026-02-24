<!-- app/Views/partials/kvapro_sidebar.php -->
<div class="overlay" id="overlay" onclick="closeMobileSidebar()"></div>

<nav class="sidebar" id="sidebar">
    <div class="brand-box">
        <div class="logo-area">
            <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo do Sistema">
        </div>
        <button class="close-sidebar-btn" onclick="closeMobileSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="nav-links">
        <a href="<?= base_url('dashboard') ?>" class="nav-item <?= url_is('dashboard') ? 'active' : '' ?>">
            <i class="fas fa-home"></i> <span>Início</span>
        </a>

        <a href="<?= base_url('projeto/novo') ?>" class="nav-item <?= url_is('projeto/novo') ? 'active' : '' ?>">
            <i class="fas fa-plus-circle"></i> <span>Novo Projeto</span>
        </a>

        <a href="<?= base_url('admin/kits') ?>" class="nav-item <?= url_is('admin/kits*') ? 'active' : '' ?>">
            <i class="fas fa-cubes"></i> <span>Kits de Montagem</span>
        </a>

        <a href="<?= base_url('admin/materiais') ?>" class="nav-item <?= url_is('admin/materiais*') ? 'active' : '' ?>">
            <i class="fas fa-tools"></i> <span>Materiais Base</span>
        </a>

        <a href="<?= base_url('admin/regras-materiais') ?>" class="nav-item <?= url_is('admin/regras-materiais*') ? 'active' : '' ?>">
            <i class="fas fa-brain"></i> <span>Regras de Automação</span>
        </a>


        <!-- <a href="<?= base_url('dashboard') ?>" class="nav-item active">
            <i class="fas fa-home"></i> <span>Início</span>
        </a>
        <a href="<?= base_url('projeto/novo') ?>" class="nav-item">
            <i class="fas fa-plus-circle"></i> <span>Novo Projeto</span>
        </a>
        <a href="<?= base_url('admin/kits') ?>" class="nav-item">
            <i class="fas fa-cubes"></i> <span>Kits de Montagem</span>
        </a>
        <a href="<?= base_url('admin/materiais') ?>" class="nav-item">
            <i class="fas fa-tools"></i> <span>Materiais Base</span>
        </a>
        <a href="<?= base_url('admin/regras-materiais') ?>" class="nav-item">
            <i class="fas fa-brain"></i> <span>Regras de Automação</span>
        </a> -->
        <a href="<?= base_url('projeto/lista-materiais') ?>" class="nav-item"><i class="fas fa-layer-group"></i> <span>Meus Projetos</span></a>

        <a href="#" class="nav-item"><i class="fas fa-bolt"></i> <span>Cálculos</span></a>
        <a href="#" class="nav-item"><i class="fas fa-file-contract"></i> <span>Documentos</span></a>
        <a href="#" class="nav-item"><i class="fas fa-cog"></i> <span>Configurações</span></a>

    </div>

    <div class="sidebar-footer">
        <div class="footer-mini-logo" id="sidebarMiniLogo">E</div>
    </div>
</nav>
<!-- Fim Sidebar -->