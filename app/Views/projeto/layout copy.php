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
    // Configurações de Layout
    $scale = 0.4;
    $maxRows = 3;
    $gap = 20 * $scale;
    $baseW = 300;
    $baseH = 560;
    $w = $baseW * $scale;
    $h = $baseH * $scale;

    // 1. Calculamos as colunas de medição e a posição da coluna central
    $colsMedicao = ceil($total_medidores / $maxRows);
    $colCentralIndex = floor($colsMedicao / 2); // Define onde o barramento entra
    $totalCols = $colsMedicao + 1; // Medições + 1 coluna de barramento

    $viewWidth = ($totalCols * ($w + $gap)) + $gap;
    $viewHeight = ($maxRows * ($h + $gap)) + $gap;
    ?>

    <svg width="<?= $viewWidth ?>" height="<?= $viewHeight ?>" viewBox="0 0 <?= $viewWidth ?> <?= $viewHeight ?>" xmlns="http://www.w3.org/2000/svg" style="background:white; border:1px solid #000">

        <?php for ($r = 0; $r < $maxRows; $r++):
            $posXCentral = $gap + ($colCentralIndex * ($w + $gap));
            $posYCentral = $gap + ($r * ($h + $gap));
        ?>
            <g transform="translate(<?= $posXCentral ?>, <?= $posYCentral ?>) scale(<?= $scale ?>)">
                <rect x="0" y="0" width="300" height="560" fill="#f0f0f0" stroke="black" stroke-width="4" />
                <rect x="15" y="15" width="270" height="530" fill="none" stroke="black" stroke-width="8" rx="10" />

                <text x="150" y="280" font-family="Arial" font-size="30" font-weight="bold" text-anchor="middle">
                    <?= ($r == 0) ? "GERAL" : "BARRAMENTO" ?>
                </text>
            </g>
        <?php endfor; ?>

        <?php foreach ($unidades as $index => $unidade):
            $row = $index % $maxRows;
            $col = floor($index / $maxRows);

            // Se a coluna da medição for igual ou maior que a central, empurramos +1 para a direita
            if ($col >= $colCentralIndex) {
                $col++;
            }

            $posX = $gap + ($col * ($w + $gap));
            $posY = $gap + ($row * ($h + $gap));
        ?>
            <g transform="translate(<?= $posX ?>, <?= $posY ?>) scale(<?= $scale ?>)">
                <rect x="0" y="0" width="300" height="560" fill="white" stroke="black" stroke-width="4" />
                <rect x="15" y="15" width="270" height="530" fill="none" stroke="black" stroke-width="8" rx="10" />

                <circle cx="150" cy="160" r="90" fill="white" stroke="black" stroke-width="3" />
                <rect x="90" y="140" width="120" height="40" fill="none" stroke="black" stroke-width="2" />
                <rect x="60" y="260" width="180" height="30" fill="none" stroke="black" stroke-width="2" />

                <?php
                $polos = (int)($unidade['mbt'] ?? 1); // Padrão unipolar se não definido
                $larguraModulo = 25; // Largura de cada polo
                $alturaDisjuntor = 80;
                $totalLargura = $polos * $larguraModulo;

                // Posicionamento base (ajustado para expandir para a esquerda conforme ganha polos)
                $startX = 240 - $totalLargura;
                $startY = 400;
                ?>

                <rect x="<?= $startX ?>" y="<?= $startY ?>" width="<?= $totalLargura ?>" height="<?= $alturaDisjuntor ?>" fill="none" stroke="black" stroke-width="2" />

                <?php for ($i = 1; $i < $polos; $i++): ?>
                    <line x1="<?= $startX + ($i * $larguraModulo) ?>" y1="<?= $startY ?>"
                        x2="<?= $startX + ($i * $larguraModulo) ?>" y2="<?= $startY + $alturaDisjuntor ?>"
                        stroke="black" stroke-width="1" />
                <?php endfor; ?>

                <line x1="<?= $startX + 5 ?>" y1="<?= $startY + ($alturaDisjuntor / 2) ?>"
                    x2="<?= $startX + $totalLargura - 5 ?>" y2="<?= $startY + ($alturaDisjuntor / 2) ?>"
                    stroke="black" stroke-width="3" stroke-linecap="round" />

                <text x="<?= $startX + 5 ?>" y="<?= $startY + ($alturaDisjuntor * 1.5) ?>" font-family="Arial" font-size="30" font-weight="bold">
                    <?= $unidade['disjuntor'] ?? '' ?>
                </text>

                <text x="30" y="440" font-family="Arial" font-size="30" font-weight="bold">
                    <?= $unidade['placa'] ?? 'C.' . sprintf("%02d", $index + 1) ?>
                </text>
            </g>
        <?php endforeach; ?>
    </svg>

</body>