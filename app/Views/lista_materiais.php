<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Lista de Materiais - <?= esc($cabecalho['obra']) ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 40px;
            background: #f9f9f9;
            color: #333;
        }

        .page-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .header-section {
            margin-bottom: 30px;
            border-bottom: 2px solid #0f2649;
            padding-bottom: 10px;
        }

        h1 {
            color: #0f2649;
            margin: 0 0 10px 0;
            font-size: 24px;
            text-transform: uppercase;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 14px;
        }

        h2 {
            background: #eef2ff;
            color: #0f2649;
            padding: 8px 15px;
            font-size: 14px;
            margin-top: 25px;
            margin-bottom: 0;
            border-left: 5px solid #0f2649;
            font-weight: bold;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 14px;
        }

        th {
            background-color: #f8f9fa;
            color: #444;
            text-align: center;
            padding: 8px;
            border-bottom: 2px solid #ddd;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
        }

        td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        /* Larguras das Colunas */
        .col-item {
            width: 8%;
            text-align: center;
            font-weight: bold;
            color: #666;
        }

        .col-desc {
            width: 62%;
            text-align: left !important;
        }

        .col-qtd {
            width: 15%;
            text-align: center;
            font-weight: bold;
        }

        .col-unid {
            width: 15%;
            text-align: center;
            color: #666;
            font-size: 12px;
        }

        th.col-desc {
            text-align: left;
        }

        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #0f2649;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        @media print {
            .btn-print {
                display: none;
            }

            body {
                background: white;
                padding: 0;
            }

            .page-container {
                box-shadow: none;
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <button onclick="window.print()" class="btn-print">IMPRIMIR</button>

    <div class="page-container">

        <div class="header-section">
            <h1>Lista de Materiais Consolidada</h1>
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

        <?php if (!empty($materiais)): ?>

            <h2>Relação de Itens (A-Z)</h2>

            <table>
                <thead>
                    <tr>
                        <th class="col-item">#</th>
                        <th class="col-desc">Descrição do Material</th>
                        <th class="col-qtd">Qtd.</th>
                        <th class="col-unid">Unid.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $contador = 1;
                    foreach ($materiais as $row):
                        $qtdDisplay = ($row['qtd'] === '' || $row['qtd'] == 0) ? '&nbsp;' : $row['qtd'];
                    ?>
                        <tr>
                            <td class="col-item"><?= $contador++ ?></td>
                            <td class="col-desc"><?= esc($row['descricao']) ?></td>
                            <td class="col-qtd"><?= $qtdDisplay ?></td>
                            <td class="col-unid"><?= esc($row['unidade']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>
            <div style="padding: 20px; text-align: center; color: #888; border: 1px dashed #ccc; margin-top: 20px;">
                Nenhum material foi gerado para este projeto. Verifique as regras de seleção.
            </div>
        <?php endif; ?>

        <?php if (!empty($materiais)): ?>

            <div style="page-break-before: always; margin-top: 50px; border-top: 2px dashed #ccc; padding-top: 30px;"></div>

            <h2 style="background: #fff; border-left: 5px solid #666; color: #444;">Memória de Enquadramento (Auditoria)</h2>
            <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Abaixo, a justificativa técnica para a inclusão de cada item nesta lista.</p>

            <table>
                <thead>
                    <tr>
                        <th class="col-desc" style="width: 50%;">Descrição do Material</th>
                        <th class="col-desc" style="width: 50%; text-align: left; color: #0056b3;">Regra Aplicada / Origem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($materiais as $row): ?>
                        <tr>
                            <td class="col-desc" style="font-size: 12px;"><?= esc($row['descricao']) ?></td>
                            <td class="col-desc" style="font-size: 11px; color: #555;">
                                <span style="display:inline-block; padding: 2px 6px; background: #f0f4f8; border-radius: 4px; border: 1px solid #e1e8ed;">
                                    <?= esc($row['origem']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>

        <div style="margin-top: 40px; border-top: 1px solid #ccc; padding-top: 10px; font-size: 11px; color: #888; text-align: center;">
            KvaPro - Projeto Elétrico Simplificado
        </div>

    </div>
</body>

</html>