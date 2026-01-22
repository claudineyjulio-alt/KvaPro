<?php namespace App\Models;

use CodeIgniter\Model;

class KitMaterialModel extends Model
{
    protected $table = 'kit_itens';
    
    // Método Mágico: Busca os itens do kit e SUBSTITUI as variáveis
    public function buscarItensProcessados($slugKit, $dadosProjeto)
    {
        $db = \Config\Database::connect();
        
        // 1. Descobre o ID do kit pelo slug
        $kit = $db->table('kits')->where('slug', $slugKit)->get()->getRow();
        if (!$kit) return [];

        // 2. Busca os materiais desse kit (JOIN)
        $itens = $db->table('kit_itens')
            ->select('materiais.descricao, materiais.unidade, kit_itens.quantidade')
            ->join('materiais', 'materiais.id = kit_itens.material_id')
            ->where('kit_itens.kit_id', $kit->id)
            ->get()
            ->getResultArray();

        // 3. Processa substituição de variáveis (Coringas)
        $itensProcessados = [];
        foreach ($itens as $item) {
            $descFinal = $item['descricao'];
            
            // Procura padrões como {variavel}
            preg_match_all('/\{(.*?)\}/', $descFinal, $matches);
            
            foreach ($matches[1] as $variavel) {
                // Se existe essa variável no JSON do projeto, substitui
                if (isset($dadosProjeto[$variavel])) {
                    $valor = $dadosProjeto[$variavel];
                    $descFinal = str_replace('{' . $variavel . '}', $valor, $descFinal);
                } else {
                    // Se não achar, remove o placeholder ou deixa genérico
                    $descFinal = str_replace('{' . $variavel . '}', '(Definir)', $descFinal);
                }
            }

            // Formata retorno para o padrão da view
            $itensProcessados[] = [
                'item'      => 'Acessório / Componente', // Pode melhorar isso depois adicionando categoria no material
                'descricao' => $descFinal,
                'qtd'       => ($item['quantidade'] > 0 ? (float)$item['quantidade'] : ''), // Se for 0 deixa vazio
                'unidade'   => $item['unidade']
            ];
        }

        return $itensProcessados;
    }
}