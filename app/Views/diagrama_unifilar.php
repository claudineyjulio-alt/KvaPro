<?php
// =================================================================
// TRATAMENTO DE DADOS (PHP -> JS)
// =================================================================
// Aqui adaptamos os dados do seu banco (Controller) para o formato que o JS espera.

// 1. Prepara dados da ENTRADA + ATERRAMENTO + DPS
// Se você ainda não tem esses campos no banco, estou definindo padrões aqui.
$dadosEntradaJS = [
    'txtOrigem'      => 'VEM DA CONCESSIONÁRIA', // Texto fixo ou vindo do banco
    'caboEntrada'    => $entrada['cabo'] ?? '3#35(35)mm²',
    'disjEntrada'    => $entrada['disjuntor'] ?? '100A',
    'fases'          => ($entrada['fases'] == 3) ? 'ABC' : (($entrada['fases'] == 2) ? 'AB' : 'A'),
    'tuboEntrada'    => $entrada['eletroduto'] ?? 'Ø 2"',
    
    // Dados do DPS (Você precisará criar campos no banco para isso futuramente)
    'caboDPS'        => $entrada['cabo_dps'] ?? '1#10mm²',
    'tensaoDPS'      => $entrada['tensao_dps'] ?? '275V',
    'capacidadeDPS'  => $entrada['ka_dps'] ?? '20kA',
    
    // Dados do Aterramento
    'caboTerra'      => $entrada['cabo_terra'] ?? 'COBRE NU #16mm²',
    'tuboTerra'      => $entrada['eletroduto_terra'] ?? 'Ø 3/4"',
    'hastesSpec'     => $entrada['hastes'] ?? '3 HASTES ALTA CAMADA 2.40m'
];

// 2. Prepara dados das UNIDADES (Medições)
$unidadesJS = [];
foreach ($medicoes as $index => $m) {
    
    // LÓGICA CORRIGIDA: 
    // 1. Tenta pegar a fase específica que veio do Controller (Ex: "AC", "BC")
    // 2. Se estiver vazia, usa a lógica antiga baseada no número (Fallback)
    $txtFases = $m['fases_especificas'] ?? '';

    if (empty($txtFases)) {
        $txtFases = 'A'; // Padrão Monofásico
        if (isset($m['fases']) && $m['fases'] == 2) $txtFases = 'AB';
        if (isset($m['fases']) && $m['fases'] == 3) $txtFases = 'ABC';
    }

    $unidadesJS[] = [
        'id'    => $m['nome'],          
        'uc'    => $m['uc_id'],         
        'cabo'  => $m['cabo'],          
        'disj'  => $m['disj'],          
        'fases' => $txtFases,           // Agora vai passar "AC", "BC", etc. corretamente
        'tubo'  => $m['eletroduto'],    
        'ramal' => str_pad($index + 1, 2, '0', STR_PAD_LEFT) 
    ];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Diagrama Unifilar Técnico - Versão Final</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>">
<style>
        /* CSS LIMPO: Ajustado para rolagem correta em telas pequenas */
        body { 
            font-family: 'Liberation Sans', sans-serif; 
            background: #ffffff; 
            margin: 0; 
            padding: 20px;
            
            /* TRUQUE PARA CENTRALIZAR + ROLAGEM: */
            text-align: center; /* Centraliza horizontalmente se couber na tela */
            overflow: auto;     /* Garante barras de rolagem */
        }

        .canvas-container { 
            background: white; 
            border: none; 
            box-shadow: none;
            
            /* Comporta-se como bloco, mas respeita o text-align do pai */
            display: inline-block; 
            text-align: left; /* Reseta o texto interno para esquerda */
        }
        
        /* Oculta botão de impressão na hora de imprimir */
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="position: fixed; top: 10px; right: 10px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #0f2649; color: white; border: none; font-weight: bold;">IMPRIMIR</button>
    </div>

    <div class="canvas-container" id="svg-output"></div>

    <script>
        // =================================================================
        // 1. OBJETOS DE DADOS (VINDO DO PHP)
        // =================================================================
        
        // Recebe o JSON gerado pelo PHP
        const entrada = <?= json_encode($dadosEntradaJS, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
        const unidades = <?= json_encode($unidadesJS, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;

        // =================================================================
        // 2. PARÂMETROS DE LAYOUT (ESCALA E POSICIONAMENTO)
        // =================================================================
        const ESCALA_GERAL = 6;    // Controla o zoom do diagrama
        const PASSO_Y = 10;        // Espaçamento entre ramais solicitado
        const X_BAR = 76.1;        // Coordenada do barramento principal
        const Y_START = 45;        // Início da primeira linha de saída

        // Função para gerar os traços de polos (I, II, III) no arco do disjuntor
        function getPolos(f) { return "I".repeat(f.trim().length); }

        function gerar() {
            const n = unidades.length;
            const yDPS = Y_START + (n * PASSO_Y); 
            const yEntrada = (Y_START + yDPS) / 2; // Centralização matemática
            const yQuadroEnd = yDPS + 20;          // Base do quadro interno
            const yMalhaTerraExt = yQuadroEnd + 15; // Base da malha externa

            // Definição do recorte (viewBox) para margem mínima
            const xMin = 25, xMax = 158;
            const yMin = 25, yMax = yMalhaTerraExt + 12;
            const larguraCoord = xMax - xMin;
            const alturaCoord = yMax - yMin;

            let svg = `<svg width="${larguraCoord * ESCALA_GERAL}px" height="${alturaCoord * ESCALA_GERAL}px" viewBox="${xMin} ${yMin} ${larguraCoord} ${alturaCoord}" xmlns="http://www.w3.org/2000/svg">
                <style>
                    text { font-family: 'Liberation Sans', sans-serif; fill: #000; }
                    .t-tiny { font-size: 1.8px; text-anchor: middle; }
                    .t-id { font-size: 2.8px; font-weight: bold; }
                    .t-med { font-size: 4.2px; text-anchor: middle; }
                    .t-iiil { font-size: 2.8px; text-anchor: middle; dominant-baseline: central; }
                    .t-fase { font-size: 1.8px; font-weight: bold; text-anchor: middle; }
                    .t-vert { font-size: 1.8px; text-anchor: start; }
                    .line { stroke: #000; fill: none; stroke-width: 0.13; }
                    .dash { stroke-dasharray: 1, 1; }
                    .quadro { stroke: #000; fill: none; stroke-width: 0.1; stroke-dasharray: 1.2, 1.2; }
                    .moldura { stroke: #000; fill: none; stroke-width: 0.3; }
                </style>

                <rect x="${xMin + 0.5}" y="${yMin + 0.5}" width="${larguraCoord - 1}" height="${alturaCoord - 1}" class="moldura" />

                <rect x="47" y="${Y_START - 10}" width="108" height="${yQuadroEnd - (Y_START - 10)}" class="quadro" />

                <path d="M ${X_BAR}, ${Y_START - 5} V ${yDPS + 5}" style="stroke-width:0.4; stroke:#000; fill:none;" />
            `;

            // --- 6. COMPONENTE: ENTRADA CONCESSIONÁRIA ---
            const ye = yEntrada;
            svg += `
            <g id="entrada">
                <path d="M 33.3,${ye} H 50.1 M 54.8,${ye} H ${X_BAR}" class="line" />
                <path d="M 31.8,${ye-0.5} v 1 l 1.8,-0.5 z" fill="#000" /> <text x="${-ye - 10}" y="30.7" style="font-size:1.8px; text-anchor:start" transform="rotate(-90)">${entrada.txtOrigem}</text>
                <text x="41.3" y="${ye}" class="t-iiil" transform="rotate(180,41.3,${ye})">${getPolos(entrada.fases)}L</text>
                <text x="63.1" y="${ye}" class="t-iiil" transform="rotate(180,63.1,${ye})">${getPolos(entrada.fases)}L</text>
                <text x="41.3" y="${ye-1.8}" class="t-tiny">${entrada.caboEntrada}</text>
                <text x="63.1" y="${ye-1.8}" class="t-tiny">${entrada.caboEntrada}</text>
                <text x="41.7" y="${ye+3.2}" class="t-tiny">${entrada.tuboEntrada}</text>
                <circle cx="50.4" cy="${ye}" r="0.4" fill="#000" /><circle cx="54.8" cy="${ye}" r="0.4" fill="#000" />
                <path d="M 50.8,${ye-1} A 2 1.5 0 0 1 54.4,${ye-1}" class="line" />
                <text x="52.6" y="${ye-1.2}" class="t-fase">${getPolos(entrada.fases)}</text>
                <text x="53.0" y="${ye-3.2}" class="t-tiny" style="font-weight:bold">${entrada.disjEntrada}</text>
            </g>`;

            // --- 7. COMPONENTE: RAMAIS DAS UNIDADES (SAÍDAS) ---
            unidades.forEach((u, i) => {
                const y = Y_START + (i * PASSO_Y);
                svg += `
                <g id="ramal-${u.id}">
                    <path d="M ${X_BAR},${y} H 93.3 M 98.1,${y} H 112.1 M 117.2,${y} H 130.6" class="line" />
                    
                    <text x="84.5" y="${y}" class="t-iiil" transform="rotate(180,84.5,${y})">${getPolos(u.fases)}L</text>
                    <text x="104.8" y="${y}" class="t-iiil" transform="rotate(180,104.8,${y})">${getPolos(u.fases)}L</text>
                    <text x="123.7" y="${y}" class="t-iiil" transform="rotate(180,123.7,${y})">${getPolos(u.fases)}L</text>

                    <text x="84.9" y="${y-1.7}" class="t-tiny">${u.cabo}</text>
                    <text x="105.3" y="${y-1.8}" class="t-tiny">${u.cabo}</text>
                    <text x="124.2" y="${y-1.8}" class="t-tiny">${u.cabo}</text>

                    <circle cx="95.7" cy="${y}" r="2.4" class="line" />
                    <text x="95.7" y="${y+1.4}" class="t-med">M</text>

                    <circle cx="112.6" cy="${y}" r="0.4" fill="#000" /><circle cx="116.9" cy="${y}" r="0.4" fill="#000" />
                    <path d="M 113.1,${y-1} A 2 1.5 0 0 1 116.4,${y-1}" class="line" />
                    <text x="114.75" y="${y-1.2}" class="t-fase">${getPolos(u.fases)}</text>
                    <text x="114.9" y="${y-3.2}" class="t-tiny">${u.disj}</text>

                    <text x="133.2" y="${y+0.2}" class="t-id">${u.id}</text>
                    <text x="133.2" y="${y+2.8}" class="t-tiny" style="text-anchor:start; font-weight:bold">Fase ${u.fases}</text>
                    ${ u.uc && u.uc.trim() !== "" ? `<text x="133.1" y="${y+5.0}" class="t-tiny" style="text-anchor:start">UC: ${u.uc}</text>` : '' }
                    <text x="124.6" y="${y+3.1}" class="t-tiny">${u.tubo}</text>
                    
                    <path d="m 130.6, ${y-0.5} v 1.0 l 1.8, -0.5 z" fill="#000" />
                    <text x="${X_BAR+1}" y="${y-3.5}" style="font-size:1.4px; text-anchor:start">RAMAL DE ENTRADA ${u.ramal}</text>
                </g>`;
            });

            // --- 8. COMPONENTE: DPS (PROTEÇÃO) ---
            svg += `
            <g id="bloco-dps">
                <path d="M ${X_BAR},${yDPS} H 93.3" class="line" />
                <rect x="93.3" y="${yDPS-1.7}" width="9.1" height="3.5" class="line" />
                <text x="97.8" y="${yDPS+1}" class="t-tiny" style="font-weight:bold">DPS</text>
                <text x="97.8" y="${yDPS-2.5}" class="t-tiny" style="font-weight:bold">${entrada.tensaoDPS}</text>
                <text x="97.8" y="${yDPS+4.2}" class="t-tiny" style="font-weight:bold">${entrada.capacidadeDPS}</text>
                
                <text x="84.5" y="${yDPS}" class="t-iiil" transform="rotate(180,84.5,${yDPS})">${getPolos(entrada.fases)}</text>
                <text x="84.9" y="${yDPS-1.7}" class="t-tiny">${entrada.caboDPS}</text>

                <path d="M 102.4,${yDPS} H 113.4 V ${yQuadroEnd - 10}" class="line dash" />
                <g transform="translate(113.4, ${yQuadroEnd - 10})">
                    <path d="M -3,0 h 6 M -1.8,1.2 h 3.6 M -0.8,2.4 h 1.6" class="line" />
                </g>
            </g>`;

            // --- 9. COMPONENTE: MALHA DE TERRA EXTERNA (3 HASTES) ---
            const xH = [54.5, 67.5, 80.5];
            svg += `
            <g id="malha-externa">
                <path d="M 54.5,${yQuadroEnd} V ${yMalhaTerraExt}" class="line dash" />
                <ellipse cx="54.5" cy="${yQuadroEnd}" rx="0.3" ry="0.3" fill="#000" />

                <path d="M ${xH[0]},${yMalhaTerraExt} H ${xH[2]}" class="line dash" />
                
                ${xH.map(x => `
                    <path d="M ${x},${yMalhaTerraExt} v 3" class="line dash" />
                    <path d="M ${x-3.4},${yMalhaTerraExt+3} h 6.8 M ${x-1.8},${yMalhaTerraExt+4.4} h 3.6 M ${x-0.9},${yMalhaTerraExt+5.6} h 1.8" class="line" />
                    <circle cx="${x}" cy="${yMalhaTerraExt}" r="0.3" fill="#000" />
                `).join('')}

                <text x="51.5" y="${yMalhaTerraExt-2}" class="t-tiny">${entrada.tuboTerra}</text>
                <text x="56.2" y="${yMalhaTerraExt-4.5}" class="t-tiny" style="text-anchor:start">${entrada.caboTerra}</text>
                <text x="97.4" y="${yMalhaTerraExt+0.5}" class="t-tiny" style="text-anchor:start">${entrada.hastesSpec}</text>
            </g>`;

            svg += `</svg>`;
            document.getElementById('svg-output').innerHTML = svg;
        }

        gerar();
    </script>
</body>
</html>