<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MateriaisIniciais extends Seeder
{
    public function run()
    {
        $data = [
            ['descricao' => 'Poste galvanizado', 'unidade' => 'Peça'],
            ['descricao' => 'Tampão', 'unidade' => 'Peça'],
            ['descricao' => 'Armação', 'unidade' => 'Peça'],
            ['descricao' => 'Roldana', 'unidade' => 'Peça'],
            ['descricao' => 'Parafuso 5/8"', 'unidade' => 'Peça'],
            ['descricao' => 'Caixa de medição p/ agrupamento', 'unidade' => 'Peça'],
            ['descricao' => 'Caixa disj. geral p/ agrupamento', 'unidade' => 'Peça'],
            ['descricao' => 'Cotovelo 1 1/2"', 'unidade' => 'Peça'],
            ['descricao' => 'Caixa de barramento', 'unidade' => 'Peça'],
            ['descricao' => 'Caixa polifásica', 'unidade' => 'Peça'],
            ['descricao' => 'Caixa monofásica', 'unidade' => 'Peça'],
            ['descricao' => 'Cabeçote', 'unidade' => 'Peça'],
            ['descricao' => 'Eletroduto PVC 1 1/2"', 'unidade' => 'Peça'],
            ['descricao' => 'Eletroduto PVC', 'unidade' => 'Peça'],
            ['descricao' => 'Eletroduto PVC 3/4"', 'unidade' => 'Peça'],
            ['descricao' => 'Eletroduto GALV. 1 1/2" x 6m', 'unidade' => 'Peça'],
            ['descricao' => 'Eletroduto GALV. 1 1/2" x 3m', 'unidade' => 'Peça'],
            ['descricao' => 'Bucha eletroduto 1 1/2"', 'unidade' => 'Peça'],
            ['descricao' => 'Bucha eletroduto 3/4"', 'unidade' => 'Peça'],
            ['descricao' => 'Bucha eletroduto', 'unidade' => 'Peça'],
            ['descricao' => 'Arruela eletroduto', 'unidade' => 'Peça'],
            ['descricao' => 'Curva eletroduto galvanizada', 'unidade' => 'Peça'],
            ['descricao' => 'Curva eletroduto PVC', 'unidade' => 'Peça'],
            ['descricao' => 'Curva eletroduto', 'unidade' => 'Peça'],
            ['descricao' => 'Luva uniduti', 'unidade' => 'Peça'],
            ['descricao' => 'Luva PVC', 'unidade' => 'Peça'],
            ['descricao' => 'Luva Galvanizada', 'unidade' => 'Peça'],
            ['descricao' => 'Unidutti cônico 1 1/2"', 'unidade' => 'Peça'],
            ['descricao' => 'Unidutti cônico', 'unidade' => 'Peça'],
            ['descricao' => 'Fita perfurada', 'unidade' => 'm'],
            ['descricao' => 'Parafuso sextavado zinc. 1/4" x 2"', 'unidade' => 'Peça'],
            ['descricao' => 'Porca zincada 1/4"', 'unidade' => 'Peça'],
            ['descricao' => 'Arruela zincada 1/4"', 'unidade' => 'Peça'],
            ['descricao' => 'Canaflex PVC 1 1/2"', 'unidade' => 'm'],
            ['descricao' => 'Canaflex PVC', 'unidade' => 'm'],
            ['descricao' => 'Fita isolante Scott 3M', 'unidade' => 'Peça'],
            ['descricao' => 'Fita de autofusão', 'unidade' => 'Peça'],
            ['descricao' => 'Cabo HEPR', 'unidade' => 'm'],
            ['descricao' => 'Caixa de inspeção PVC', 'unidade' => 'Peça'],
            ['descricao' => 'Conector grampo duplo', 'unidade' => 'Peça'],
            ['descricao' => 'Massa para calafetar', 'unidade' => 'Peça'],
            ['descricao' => 'Conector Ampactinho tipo', 'unidade' => 'Peça'],
            ['descricao' => 'Conector H1', 'unidade' => 'Peça'],
            ['descricao' => 'Disjuntor', 'unidade' => 'Peça'],
            ['descricao' => 'Caixa de passagem', 'unidade' => 'Peça'],
            ['descricao' => 'Abraçadeira nylon', 'unidade' => 'Peça'],
            ['descricao' => 'Terminal ilhós tub.', 'unidade' => 'Peça'],
            ['descricao' => 'Terminal compressão', 'unidade' => 'Peça']
        ];

        // Batch insert
        $this->db->table('materiais')->insertBatch($data);
    }
}