<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Materiais - <?= esc($cabecalho['obra']) ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; background: #f9f9f9; color: #333; }
        .page-container { max-width: 900px; margin: 0 auto; background: white; padding: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        .header-section { margin-bottom: 30px; border-bottom: 2px solid #0f2649; padding-bottom: 10px; }
        h1 { color: #0f2649; margin: 0 0 10px 0; font-size: 24px; text-transform: uppercase; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 14px; }
        
        h2 { 
            background: #eef2ff; color: #0f2649; 
            padding: 8px 15px; font-size: 14px; 
            margin-top: 25px; margin-bottom: 0;
            border-left: 5px solid #0f2649; 
            font-weight: bold; text-transform: uppercase;
        }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 14px; }
        th { 
            background-color: #f8f9fa; color: #444; 
            text-align: center; padding: 8px; /* Centralizado */
            border-bottom: 2px solid #ddd; font-weight: 700;
            font-size: 11px; text-transform: uppercase;
        }
        td { padding: 8px 10px; border-bottom: 1px solid #eee; vertical-align: middle; }
        tr:nth-child(even) { background-color: #fafafa; }
        
        /* Larguras das Colunas (Ajustado) */
        .col-item { width: 8%; text-align: center; font-weight: bold; color: #666; } /* Item pequeno */
        .col-desc { width: 62%; text-align: left !important; } /* Descrição larga */
        .col-qtd  { width: 15%; text-align: center; font-weight: bold; }
        .col-unid { width: 15%; text-align: center; color: #666; font-size: 12px; }

        /* Alinhamento específico para o TH da descrição */
        th.col-desc { text-align: left; }

        .btn-print { position: fixed; top: 20px; right: 20px; background: #0f2649; color: white; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; font-weight: bold; box-shadow: 0 4px 10px rgba(0,0,0,0.2); }
        @media print { .btn-print { display: none; } body { background: white; padding: 0; } .page-container { box-shadow: none; padding: 0; margin: 0; } }
    </style>
</head>
<body>

    <button onclick="window.print()" class="btn-print">IMPRIMIR</button>

    <div class="page-container">
        
        <div class="header-section">
            <h1>Lista de Materiais</h1>
            <div class="info-grid">
                <div>
                    <p><strong>Obra:</strong> <?= esc($cabecalho['obra']) ?></p>
                    <p><strong>Cliente:</strong> <?= esc($cabecalho['cliente']) ?></p>
                </div>
                <div style="text-align: right;">
                    <p><strong>Data:</strong> <?= date('d/m/Y') ?></p>
                    <p><strong>Local:</strong> <?= esc($cabecalho['endereco']) ?></p>
                </div>
            </div>
        </div>

        <?php 
        // CONTADOR GLOBAL
        $contadorGlobal = 1;

        // Função que recebe o contador por referência (&)
        function renderTabela($itens, &$contador) {
            if(empty($itens)) return;
            echo '<table>';
            echo '<thead><tr>
                    <th class="col-item">Item</th>
                    <th class="col-desc">Descrição do Material</th>
                    <th class="col-qtd">Qtd.</th>
                    <th class="col-unid">Unid.</th>
                  </tr></thead>';
            echo '<tbody>';
            foreach($itens as $row) {
                $qtdDisplay = $row['qtd'] === '' ? '&nbsp;' : $row['qtd'];
                echo '<tr>';
                // AQUI ESTÁ O CONTADOR SEQUENCIAL
                echo '<td class="col-item">' . $contador++ . '</td>';
                echo '<td class="col-desc">' . esc($row['descricao']) . '</td>';
                echo '<td class="col-qtd">' . $qtdDisplay . '</td>';
                echo '<td class="col-unid">' . esc($row['unidade']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        }
        ?>

        <?php if(!empty($entrada)): ?>
            <h2>Infraestrutura de Entrada</h2>
            <?php renderTabela($entrada, $contadorGlobal); ?>
        <?php endif; ?>

        <?php if(!empty($protecao)): ?>
            <h2>Proteção e Aterramento</h2>
            <?php renderTabela($protecao, $contadorGlobal); ?>
        <?php endif; ?>

        <?php if(!empty($medicoes)): ?>
            <h2>Medição e Ramais</h2>
            <?php renderTabela($medicoes, $contadorGlobal); ?>
        <?php endif; ?>

        <div style="margin-top: 40px; border-top: 1px solid #ccc; padding-top: 10px; font-size: 11px; color: #888; text-align: center;">
            KvaPro - Projeto Elétrico Simplificado
        </div>

    </div>
</body>
</html>