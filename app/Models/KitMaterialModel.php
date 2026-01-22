<?php 
namespace App\Models;

use CodeIgniter\Model;

class KitMaterialModel extends Model
{
    protected $table = 'kits'; 

    /**
     * Busca os itens de um kit estritamente pelo ID
     * @param int $kitId
     * @param array $dadosProjeto
     */
    public function buscarItensProcessados($kitId, $dadosProjeto)
    {
        // Se não vier um número, retorna vazio (Segurança)
        if (!is_numeric($kitId)) {
            return [];
        }

        $db = \Config\Database::connect();

        // Busca os itens fazendo o JOIN com a tabela de materiais
        $builder = $db->table('kit_itens');
        $builder->select('materiais.descricao, materiais.unidade, kit_itens.quantidade');
        $builder->join('materiais', 'materiais.id = kit_itens.material_id');
        $builder->where('kit_itens.kit_id', $kitId);
        
        $itens = $builder->get()->getResultArray();

        if (empty($itens)) {
            return [];
        }

        // Processa Placeholders
        $itensProcessados = [];

        foreach ($itens as $item) {
            $descricaoFinal = $item['descricao'];
            
            // Substituição de Variáveis: {variavel}
            if (preg_match_all('/\{(.*?)\}/', $descricaoFinal, $matches)) {
                foreach ($matches[1] as $variavel) {
                    if (isset($dadosProjeto[$variavel])) {
                        $descricaoFinal = str_replace('{' . $variavel . '}', $dadosProjeto[$variavel], $descricaoFinal);
                    } else {
                        // Limpa o placeholder se não tiver dado, ou coloca um aviso
                        $descricaoFinal = str_replace('{' . $variavel . '}', '', $descricaoFinal);
                    }
                }
            }

            $itensProcessados[] = [
                'descricao' => trim($descricaoFinal), // Trim remove espaços extras se o placeholder ficar vazio
                'qtd'       => $item['quantidade'],
                'unidade'   => $item['unidade']
            ];
        }

        return $itensProcessados;
    }
}