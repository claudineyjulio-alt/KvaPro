<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Layout Medições - <?= esc($titulo_obra ?? 'Projeto') ?></title>
    <link rel="icon" type="image/png" href="<?= function_exists('base_url') ? base_url('assets/img/favicon.png') : 'favicon.png' ?>">
    <style>
        body {
            margin: 15mm;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f0f0f0;
        }
    </style>
</head>

<body>

    <?php
// Configurações do Projeto
$temGeral = true;         // Variável booleana para Disjuntor Geral
$qtdBarramentos = ($total_medidores > 7) ? 2: 1;     // Número de caixas de barramento
$maxRows = ($total_medidores > 4) ? 3: 2;            // Máximo de linhas
$scale = 0.4;

// --- LÓGICA DE MONTAGEM DO GRID ---

$total_itens_servico = ($temGeral ? 1 : 0) + $qtdBarramentos;
// $total_medidores = count($unidades);
$total_slots_necessarios = $total_medidores + $total_itens_servico;

$totalCols = ceil($total_slots_necessarios / $maxRows);
$colCentralIndex = floor($total_slots_necessarios / $maxRows / 2);

// Criamos um mapa do Grid (Matriz) para distribuir os itens
$grid = array_fill(0, $totalCols, array_fill(0, $maxRows, null));

// 1. Posicionar Itens de Serviço na Coluna Central
$currentRow = 0;
if ($temGeral) {
    $grid[$colCentralIndex][$currentRow++] = ['tipo' => 'GERAL'];
}
for ($i = 0; $i < $qtdBarramentos; $i++) {
    if ($currentRow < $maxRows) {
        $grid[$colCentralIndex][$currentRow++] = ['tipo' => 'BARRAMENTO'];
    }
}

// 2. Preencher espaços vazios com as medições (Unidades)
$unidadeIdx = 0;
for ($c = 0; $c < $totalCols; $c++) {
    for ($r = 0; $r < $maxRows; $r++) {
        if ($grid[$c][$r] === null && $unidadeIdx < $total_medidores) {
            $grid[$c][$r] = array_merge($unidades[$unidadeIdx], ['tipo' => 'MEDICAO']);
            $unidadeIdx++;
        }
    }
}

// Dimensões para o SVG
$baseW = 300; $baseH = 560;
$w = $baseW * $scale; $h = $baseH * $scale;
$gap = 20 * $scale;
$viewWidth = ($totalCols * ($w + $gap)) + $gap;
$viewHeight = ($maxRows * ($h + $gap)) + $gap;
?>

<svg width="<?= $viewWidth ?>" height="<?= $viewHeight ?>" viewBox="0 0 <?= $viewWidth ?> <?= $viewHeight ?>" xmlns="http://www.w3.org/2000/svg" style="background:white; border:1px solid #000">

    <?php foreach ($grid as $c => $coluna): ?>
        <?php foreach ($coluna as $r => $item): 
            if (!$item) continue;
            
            $posX = $gap + ($c * ($w + $gap));
            $posY = $gap + ($r * ($h + $gap));
        ?>
            <g transform="translate(<?= $posX ?>, <?= $posY ?>) scale(<?= $scale ?>)">
                
                <?php if ($item['tipo'] === 'GERAL' || $item['tipo'] === 'BARRAMENTO'): ?>
                    <rect x="0" y="0" width="300" height="560" fill="#f0f0f0" stroke="black" stroke-width="4" />
                    <rect x="15" y="15" width="270" height="530" fill="none" stroke="black" stroke-width="8" rx="10" />
                    <text x="150" y="280" font-family="Arial" font-size="40" font-weight="bold" text-anchor="middle">
                        <?= $item['tipo'] ?>
                    </text>

                <?php else: ?>
                    <rect x="0" y="0" width="300" height="560" fill="white" stroke="black" stroke-width="4" />
                    <rect x="15" y="15" width="270" height="530" fill="none" stroke="black" stroke-width="8" rx="10" />
                    <circle cx="150" cy="160" r="90" fill="white" stroke="black" stroke-width="3" />
                    <rect x="90" y="140" width="120" height="40" fill="none" stroke="black" stroke-width="2" />
                    <rect x="60" y="260" width="180" height="30" fill="none" stroke="black" stroke-width="2" />

                    <?php
                        $polos = (int)($item['categoria'] ?? 1);
                        $larguraModulo = 25;
                        $totalLargura = $polos * $larguraModulo;
                        $startX = 240 - $totalLargura;
                        $startY = 400;
                    ?>
                    <rect x="<?= $startX ?>" y="<?= $startY ?>" width="<?= $totalLargura ?>" height="80" fill="none" stroke="black" stroke-width="2" />
                    <?php for ($i = 1; $i < $polos; $i++): ?>
                        <line x1="<?= $startX + ($i * $larguraModulo) ?>" y1="<?= $startY ?>" x2="<?= $startX + ($i * $larguraModulo) ?>" y2="<?= $startY + 80 ?>" stroke="black" stroke-width="1" />
                    <?php endfor; ?>
                    <line x1="<?= $startX + 5 ?>" y1="<?= $startY + 40 ?>" x2="<?= $startX + $totalLargura - 5 ?>" y2="<?= $startY + 40 ?>" stroke="black" stroke-width="3" />

                    <text x="<?= $startX ?>" y="<?= $startY + 115 ?>" font-family="Arial" font-size="30" font-weight="bold"><?= $item['disjuntor'] ?? '' ?></text>
                    <text x="30" y="440" font-family="Arial" font-size="30" font-weight="bold"><?= $item['placa'] ?></text>
                <?php endif; ?>

            </g>
        <?php endforeach; ?>
    <?php endforeach; ?>
</svg>

</body>