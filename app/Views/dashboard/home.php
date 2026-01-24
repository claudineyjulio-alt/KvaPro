<?= $this->extend('layouts/kvapro_layout') ?>

<?= $this->section('title') ?>Home<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-body">
    <div id="mobileQuickAccess" class="mobile-quick-access"></div>

    <div class="welcome-banner">
        <div>
            <h2>Olá, Claudinei!</h2>
            <p style="opacity: 0.8; margin-top: 5px;">Bem-vindo ao sistema de projetos.</p>
        </div>
        <button
            style="background: var(--navy-accent); color: var(--navy-dark); border: none; padding: 10px 20px; border-radius: 5px; font-weight: bold; cursor: pointer;">
            <i class="fas fa-plus"></i> Novo Projeto
        </button>
    </div>

    <div class="main-grid">
        <div class="projects-section">
            <div style="font-size: 1.2rem; color: var(--navy-dark); margin-bottom: 15px; font-weight: bold;">
                <i class="fas fa-tasks"></i> Projetos Recentes
            </div>
            <div class="project-card">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <div>
                        <div style="font-weight: bold; color: var(--navy-dark);">Residencial Green Valley</div>
                        <div style="font-size: 0.85rem; color: #666;">Cliente: Construtora Moura</div>
                    </div>
                    <span
                        style="background: #e3f2fd; color: #1565c0; padding: 5px 10px; border-radius: 20px; font-size: 0.75rem; height: fit-content;">Execução</span>
                </div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: 75%;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: #777;">
                    <span><i class="far fa-clock"></i> Atualizado ontem</span>
                    <span>75% Concluído</span>
                </div>
            </div>

            <div class="project-card">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <div>
                        <div style="font-weight: bold; color: var(--navy-dark);">Subestação Indústria Têxtil
                        </div>
                        <div style="font-size: 0.85rem; color: #666;">Cliente: Fabril Textil S.A.</div>
                    </div>
                    <span
                        style="background: #fff3e0; color: #ef6c00; padding: 5px 10px; border-radius: 20px; font-size: 0.75rem; height: fit-content;">Aprovação</span>
                </div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: 40%; background: #ef6c00;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: #777;">
                    <span><i class="far fa-clock"></i> Atualizado a 2h </span>
                    <span>40% Concluído</span>
                </div>
            </div>

        </div>

        <div class="jobs-column">
            <div style="font-size: 1.2rem; color: var(--navy-dark); margin-bottom: 15px; font-weight: bold;">
                <i class="fas fa-briefcase"></i> Oportunidades
            </div>
            <div class="jobs-panel">
                <div class="job-card">
                    <div class="job-title">Coord. de Projetos Elétricos</div>
                    <div style="font-size: 0.8rem; color: #555;">Grupo Energia - Belo Horizonte</div>
                    <div class="job-salary">R$ 8.500 - R$ 10.000</div>
                    <button
                        style="width: 100%; margin-top: 10px; border: 1px solid var(--navy-dark); background: transparent; padding: 5px; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">Ver
                        Detalhes</button>
                </div>

                <div class="job-card">
                    <div class="job-title">Engenheiro de Campo (FV)</div>
                    <div style="font-size: 0.8rem; color: #555;">SolarTech - Remoto/Híbrido</div>
                    <div class="job-salary">A Combinar</div>
                    <button
                        style="width: 100%; margin-top: 10px; border: 1px solid var(--navy-dark); background: transparent; padding: 5px; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">Ver
                        Detalhes</button>
                </div>
                <button class="nav-item" onclick="showModalMessagens('Erro ao conectar com o banco de dados. Tente novamente mais tarde.')"><i class="fas fa-layer-group"></i>
                    <span>Testar Modal de Mensagens</span>
                </button>
            </div>
        </div>
    </div>

    <section class="partners-section">
        <h4
            style="text-align: center; margin-bottom: 25px; color: var(--navy-dark); text-transform: uppercase;">
            Parceiros</h4>
        <div class="partners-grid">
            <div class="partner-card" style="border-bottom: 3px solid #f39c12;">
                <span class="ad-tag">Patrocinado</span>
                <i class="fas fa-solar-panel" style="font-size: 2rem; color: #f39c12; margin-bottom: 10px;"></i>
                <div style="text-align: center;">
                    <div style="font-weight: bold; color: #333;">Solarex Distribuidora</div>
                    <div style="font-size: 0.8rem; color: #666; margin-top: 5px;">Kits Fotovoltaicos com 15% OFF
                        para Integradores.</div>
                </div>
            </div>

            <div class="partner-card" style="border-bottom: 3px solid #e74c3c;">
                <span class="ad-tag">Oferta</span>
                <i class="fas fa-tools" style="font-size: 2rem; color: #e74c3c; margin-bottom: 10px;"></i>
                <div style="text-align: center;">
                    <div style="font-weight: bold; color: #333;">Mundo das Ferramentas</div>
                    <div style="font-size: 0.8rem; color: #666; margin-top: 5px;">Alicates e Multímetros Fluke
                        em até 12x.</div>
                </div>
            </div>

            <div class="partner-card" style="border-bottom: 3px solid #2980b9;">
                <span class="ad-tag">Parceiro</span>
                <i class="fas fa-bolt" style="font-size: 2rem; color: #2980b9; margin-bottom: 10px;"></i>
                <div style="text-align: center;">
                    <div style="font-weight: bold; color: #333;">EletroForte Atacado</div>
                    <div style="font-size: 0.8rem; color: #666; margin-top: 5px;">Cabos, Disjuntores e Quadros
                        com entrega expressa.</div>
                </div>
            </div>

            <div class="partner-card" style="border-bottom: 3px solid #27ae60;">
                <span class="ad-tag">Curso</span>
                <i class="fas fa-graduation-cap"
                    style="font-size: 2rem; color: #27ae60; margin-bottom: 10px;"></i>
                <div style="text-align: center;">
                    <div style="font-weight: bold; color: #333;">Academy Automation</div>
                    <div style="font-size: 0.8rem; color: #666; margin-top: 5px;">Domine CLPs e Inversores na
                        prática. Inscreva-se!</div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
