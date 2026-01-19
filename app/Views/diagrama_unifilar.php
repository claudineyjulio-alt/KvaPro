<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagrama Unifilar - KvaPro</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap');
        
        @media print {
            @page { size: A3 landscape; margin: 5mm; } 
            body { background: white; -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
            .diagrama-container { border: none !important; box-shadow: none !important; width: 100% !important; overflow: visible !important; }
            svg { width: 100% !important; height: auto !important; }
        }

        .cad-text { font-family: 'Roboto Mono', monospace; font-size: 11px; fill: #000; }
        .cad-title { font-family: 'Roboto Mono', monospace; font-size: 14px; font-weight: bold; fill: #000; }
        .cad-small { font-family: 'Roboto Mono', monospace; font-size: 9px; fill: #000; }
        .cad-label { font-family: 'Roboto Mono', monospace; font-size: 9px; fill: #666; }
        
        .line-main { stroke: #000; stroke-width: 1.5; fill: none; }
        .line-bus { stroke: #000; stroke-width: 6; stroke-linecap: square; }

        .breaker-circle { stroke: #000; stroke-width: 1.5; fill: #fff; }
        .breaker-arc { stroke: #000; stroke-width: 1.5; fill: none; }
        .breaker-pole { stroke: #000; stroke-width: 2; fill: none; } 
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <nav class="bg-[#0f2649] text-white p-4 shadow-md no-print flex justify-between items-center">
        <div class="flex items-center gap-3">
            <span class="font-bold text-xl">Diagrama Unifilar</span>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded text-sm font-bold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                Imprimir
            </button>
            <a href="<?= base_url('dashboard') ?>" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded text-sm">Voltar</a>
        </div>
    </nav>

    <div class="flex-grow p-4 flex justify-center overflow-auto bg-gray-200">
        <div class="diagrama-box bg-white p-6 rounded shadow-lg border border-gray-300 inline-block min-w-[1100px]">
            
            <?php
                // --- DADOS MOCK ---
                $entrada = [
                    'cabo' => '3#35(35)mm²',
                    'eletroduto' => 'Ø50mm',
                    'disjuntor' => '3x100A',
                    'fases' => 3 
                ];

                $medicoes = [
                    ['nome' => 'AP. 101', 'uc_id' => '12345678', 'fases' => 2, 'cabo' => '2#10(10)', 'disj' => '2x50A', 'eletroduto' => 'Ø1 1/2"'],
                    ['nome' => 'AP. 102', 'uc_id' => '87654321', 'fases' => 2, 'cabo' => '2#10(10)', 'disj' => '2x50A', 'eletroduto' => 'Ø1 1/2"'],
                    ['nome' => 'AP. 201', 'uc_id' => '11223344', 'fases' => 2, 'cabo' => '2#10(10)', 'disj' => '2x50A', 'eletroduto' => 'Ø1 1/2"'],
                    ['nome' => 'AP. 202', 'uc_id' => '44332211', 'fases' => 2, 'cabo' => '2#10(10)', 'disj' => '2x50A', 'eletroduto' => 'Ø1 1/2"'],
                    ['nome' => 'SERV.',   'uc_id' => '99887766', 'fases' => 3, 'cabo' => '3#16(16)', 'disj' => '3x63A', 'eletroduto' => 'Ø2"'],
                ];

                // --- CONFIGURAÇÕES ---
                $alturaLinha = 75; 
                $margemY = 80;
                $larguraCanvas = 1100;
                $alturaCanvas = (count($medicoes) * $alturaLinha) + 120;
                $xBus = 350;
                
                // Cálculo simétrico da barra
                $qtdItems = count($medicoes);
                $yBarraInicio = $margemY - 40;
                $yBarraFim    = $margemY + (($qtdItems - 1) * $alturaLinha) + 40;
            ?>

            <svg width="<?= $larguraCanvas ?>" height="<?= $alturaCanvas ?>" viewBox="0 0 <?= $larguraCanvas ?> <?= $alturaCanvas ?>" xmlns="http://www.w3.org/2000/svg">
                
                <defs>
                    <g id="sym-medidor">
                        <circle cx="0" cy="0" r="16" stroke="black" stroke-width="1.5" fill="white" />
                        <text x="0" y="5" text-anchor="middle" font-family="Roboto Mono" font-size="18" fill="black">M</text>
                    </g>
                    <line id="mark-fase" x1="0" y1="-7" x2="0" y2="7" stroke="black" stroke-width="1.5" />
                    <g id="mark-neutro">
                         <line x1="0" y1="-7" x2="0" y2="7" stroke="black" stroke-width="1.5" />
                         <line x1="0" y1="-7" x2="-5" y2="-7" stroke="black" stroke-width="1.5" /> 
                    </g>
                    <g id="sym-disjuntor-base">
                        <circle cx="-14" cy="0" r="2" class="breaker-circle" />
                        <circle cx="14" cy="0" r="2" class="breaker-circle" />
                        <path d="M -14 -8 Q 0 -26 14 -8" class="breaker-arc" />
                    </g>
                </defs>

                <line x1="<?= $xBus ?>" y1="<?= $yBarraInicio ?>" x2="<?= $xBus ?>" y2="<?= $yBarraFim ?>" class="line-bus" />
                <text x="<?= $xBus - 15 ?>" y="<?= $yBarraFim ?>" text-anchor="end" class="cad-small" style="writing-mode: vertical-rl; text-orientation: mixed;">BARRAMENTO GERAL</text>


                <?php 
                    $indiceMeio = ($qtdItems - 1) / 2; 
                    $yInput = $margemY + ($indiceMeio * $alturaLinha); 
                    
                    // AJUSTE 2: Movi o texto REDE e o início da linha para evitar corte
                    $xInputStart = 60; // Começa mais à direita para dar espaço ao texto
                    $xInputCable = 150;
                    $xInputDisj = 260;
                ?>
                <g transform="translate(0, <?= $yInput ?>)">
                    
                    <text x="35" y="0" transform="rotate(-90, 35, 0)" text-anchor="middle" class="cad-title" fill="#666">REDE</text>

                    <line x1="<?= $xInputStart ?>" y1="0" x2="<?= $xBus ?>" y2="0" class="line-main" />
                    <circle cx="<?= $xBus ?>" cy="0" r="4" fill="black" />

                    <g transform="translate(<?= $xInputCable ?>, 0)">
                        <?php 
                            $totalMarkers = $entrada['fases'] + 1;
                            $startOffsetX = -( ($totalMarkers * 8) / 2 ) + 4;
                            echo '<use href="#mark-neutro" x="'.$startOffsetX.'" y="0" />';
                            for($f=0; $f < $entrada['fases']; $f++) echo '<use href="#mark-fase" x="'.($startOffsetX + (($f+1)*8)).'" y="0" />';
                        ?>
                        <text x="0" y="-12" text-anchor="middle" class="cad-text"><?= $entrada['cabo'] ?></text>
                        <text x="0" y="18" text-anchor="middle" class="cad-text"><?= $entrada['eletroduto'] ?></text>
                    </g>

                    <g transform="translate(<?= $xInputDisj ?>, 0)">
                        <line x1="-9" y1="0" x2="9" y2="0" stroke="white" stroke-width="4" />
                        
                        <use href="#sym-disjuntor-base" x="0" y="0" />
                        <?php
                            $offsetsGeral = [-7, 0, 7]; 
                            foreach($offsetsGeral as $off) echo '<line x1="'.$off.'" y1="-12" x2="'.$off.'" y2="-24" class="breaker-pole" />';
                        ?>
                        <text x="0" y="-32" text-anchor="middle" class="cad-text" font-weight="bold">GERAL <?= $entrada['disjuntor'] ?></text>
                    </g>
                </g>


                <?php foreach($medicoes as $i => $m): 
                    $y = $margemY + ($i * $alturaLinha); 
                    $startX = $xBus; 
                    
                    $xCabo1 = $startX + 130;
                    $xMedidor = $xCabo1 + 80;
                    $xCabo2 = $xMedidor + 80;
                    $xDisjuntor = $xCabo2 + 70;
                    $xFinalComponents = $xDisjuntor + 80;
                    $arrowTipX = $xFinalComponents + 100;
                    $lineEndX = $arrowTipX - 10;

                    $totalMarkers = $m['fases'] + 1;
                    $startOffsetX = -( ($totalMarkers * 8) / 2 ) + 4;
                ?>
                    <g transform="translate(0, <?= $y ?>)">
                        
                        <text x="<?= $startX + 5 ?>" y="-8" text-anchor="start" class="cad-small" font-weight="bold">RAMAL <?= str_pad($i+1, 2, '0', STR_PAD_LEFT) ?></text>

                        <line x1="<?= $startX ?>" y1="0" x2="<?= $lineEndX ?>" y2="0" class="line-main" />
                        <circle cx="<?= $startX ?>" cy="0" r="4" fill="black" />

                        <g transform="translate(<?= $xCabo1 ?>, 0)">
                            <?php 
                                echo '<use href="#mark-neutro" x="'.$startOffsetX.'" y="0" />';
                                for($f=0; $f < $m['fases']; $f++) echo '<use href="#mark-fase" x="'.($startOffsetX + (($f+1)*8)).'" y="0" />';
                            ?>
                            <text x="0" y="-12" text-anchor="middle" class="cad-text"><?= $m['cabo'] ?></text>
                        </g>

                        <use href="#sym-medidor" x="<?= $xMedidor ?>" y="0" />

                        <g transform="translate(<?= $xCabo2 ?>, 0)">
                            <?php 
                                echo '<use href="#mark-neutro" x="'.$startOffsetX.'" y="0" />';
                                for($f=0; $f < $m['fases']; $f++) echo '<use href="#mark-fase" x="'.($startOffsetX + (($f+1)*8)).'" y="0" />';
                            ?>
                            <text x="0" y="-12" text-anchor="middle" class="cad-text"><?= $m['cabo'] ?></text>
                        </g>

                        <g transform="translate(<?= $xDisjuntor ?>, 0)">
                            <line x1="-9" y1="0" x2="9" y2="0" stroke="white" stroke-width="4" />
                            
                            <use href="#sym-disjuntor-base" x="0" y="0" />
                            <?php
                                $offs = ($m['fases']==1) ? [0] : (($m['fases']==2) ? [-4,4] : [-6,0,6]);
                                foreach($offs as $off) echo '<line x1="'.$off.'" y1="-12" x2="'.$off.'" y2="-24" class="breaker-pole" />';
                            ?>
                            <text x="0" y="-32" text-anchor="middle" class="cad-text" font-weight="bold"><?= $m['disj'] ?></text>
                        </g>

                        <g transform="translate(<?= $xFinalComponents ?>, 0)">
                            <?php 
                                echo '<use href="#mark-neutro" x="'.$startOffsetX.'" y="0" />';
                                for($f=0; $f < $m['fases']; $f++) echo '<use href="#mark-fase" x="'.($startOffsetX + (($f+1)*8)).'" y="0" />';
                            ?>
                            <text x="0" y="-15" text-anchor="middle" class="cad-text"><?= $m['cabo'] ?></text>
                            <text x="0" y="25" text-anchor="middle" class="cad-text"><?= $m['eletroduto'] ?></text>
                        </g>

                        <polygon points="<?= $arrowTipX ?>,0 <?= $lineEndX ?>,-4 <?= $lineEndX ?>,4" fill="black" />
                        
                        <?php $textX = $arrowTipX + 10; ?>
                        
                        <text x="<?= $textX ?>" y="-5" class="cad-text" font-weight="bold" style="font-size: 14px;"><?= $m['nome'] ?></text>
                        <text x="<?= $textX ?>" y="10" class="cad-label" font-weight="bold" style="fill: #000;">UC: <?= $m['uc_id'] ?></text>
                        <?php $faseTxt = ($m['fases'] == 3) ? 'ABC' : (($m['fases'] == 2) ? 'AB' : 'A'); ?>
                        <text x="<?= $textX ?>" y="22" class="cad-label">FASE: <?= $faseTxt ?></text>

                    </g>
                <?php endforeach; ?>

            </svg>
        </div>
    </div>
</body>
</html>