<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Símbolos - EletCAD</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Ícones -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        /* Estilo da Miniatura */
        .thumb-svg {
            width: 40px;
            height: 40px;
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 2px;
        }
        /* Alinhamento vertical da tabela */
        .table > tbody > tr > td {
            vertical-align: middle;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0"><i class="bi bi-collection"></i> Biblioteca de Símbolos</h2>
            <p class="text-muted small mb-0">Gerencie os desenhos lógicos usados nos diagramas</p>
        </div>
        <a href="<?= base_url('simbolos/novo') ?>" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg"></i> Novo Símbolo
        </a>
    </div>

    <!-- Mensagens de Feedback -->
    <?php if(session()->getFlashdata('sucesso')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('sucesso') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Tabela de Listagem -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" width="50">ID</th>
                        <th width="80" class="text-center">Ícone</th>
                        <th>Nome do Componente</th>
                        <th>Sigla</th>
                        <th>Categoria</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($simbolos)): ?>
                        <?php foreach($simbolos as $s): ?>
                            <tr>
                                <td class="ps-4 text-muted">#<?= $s['id'] ?></td>
                                
                                <!-- COLUNA DA MINIATURA -->
                                <td class="text-center">
                                    <!-- Criamos um SVG container fixo e injetamos o código do banco dentro -->
                                    <svg viewBox="0 0 100 100" class="thumb-svg">
                                        <!-- Removemos tags <svg> externas caso existam no código salvo para evitar aninhamento -->
                                        <?= preg_replace('/<\/?svg[^>]*>/i', '', $s['simbolo_svg'] ?? '') ?>
                                    </svg>
                                </td>

                                <td class="fw-bold text-dark"><?= $s['nome'] ?></td>
                                
                                <td>
                                    <span class="badge bg-secondary text-uppercase"><?= $s['sigla_padrao'] ?></span>
                                </td>
                                
                                <td>
                                    <!-- Badges coloridos por categoria -->
                                    <?php 
                                        $cor = match($s['categoria']) {
                                            'bobina' => 'primary',
                                            'potencia' => 'danger',
                                            'comando' => 'success',
                                            'conexao' => 'warning text-dark',
                                            default => 'secondary'
                                        };
                                    ?>
                                    <span class="badge bg-<?= $cor ?> bg-opacity-10 text-<?= $cor ?> border border-<?= $cor ?>">
                                        <?= ucfirst($s['categoria']) ?>
                                    </span>
                                </td>
                                
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="<?= base_url('simbolos/editar/'.$s['id']) ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="<?= base_url('simbolos/excluir/'.$s['id']) ?>" onclick="return confirm('Tem certeza que deseja excluir este símbolo?')" class="btn btn-sm btn-outline-danger" title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-3 opacity-50"></i>
                                Nenhum símbolo cadastrado ainda.<br>
                                <a href="<?= base_url('simbolos/novo') ?>" class="text-decoration-none">Cadastre o primeiro</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white text-muted small ps-4">
            Total de registros: <strong><?= count($simbolos) ?></strong>
        </div>
    </div>
</div>

<!-- Script Bootstrap (para fechar alertas) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>