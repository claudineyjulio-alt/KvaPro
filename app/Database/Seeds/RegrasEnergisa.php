<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RegrasEnergisa extends Seeder
{
    public function run()
    {
        $idEnergisa = 1;

        // ==========================================================
        // 0. LIMPEZA (Para evitar duplicidade e erros)
        // ==========================================================
        $this->db->query('SET FOREIGN_KEY_CHECKS=0;');
        $this->db->table('regras_materiais')->truncate();
        $this->db->table('regras_normas')->truncate();
        $this->db->table('normas')->truncate();
        // CUIDADO: Isso limpa os kits antigos para teste. 
        // Se já tiver kits reais cadastrados, remova a linha abaixo.
        $this->db->table('kits')->truncate(); 
        $this->db->query('SET FOREIGN_KEY_CHECKS=1;');

        // ==========================================================
        // 1. POPULAR NORMAS
        // ==========================================================
        $normas = [
            ['id' => 1, 'concessionaria_id' => $idEnergisa, 'codigo' => 'NDU001', 'titulo' => 'Fornecimento Individual BT'],
            ['id' => 2, 'concessionaria_id' => $idEnergisa, 'codigo' => 'NDU002', 'titulo' => 'Fornecimento Individual Alta Carga'],
            ['id' => 3, 'concessionaria_id' => $idEnergisa, 'codigo' => 'NDU003', 'titulo' => 'Fornecimento Agrupado']
        ];
        $this->db->table('normas')->insertBatch($normas);

        // ==========================================================
        // 2. REGRAS DE SELEÇÃO DE NORMA
        // ==========================================================
        $regrasNormas = [
            [
                'concessionaria_id' => $idEnergisa, 'prioridade' => 1, 'descricao' => 'Agrupamento (Mais de 1 UC)',
                'variavel' => 'qtd_uc', 'condicao' => '>=', 'valor_min' => '2', 'valor_max' => null, 'norma_resultante_id' => 3
            ],
            [
                'concessionaria_id' => $idEnergisa, 'prioridade' => 2, 'descricao' => 'Individual Carga Alta (> 81.5kVA)',
                'variavel' => 'demanda_kva', 'condicao' => '>', 'valor_min' => '81.5', 'valor_max' => null, 'norma_resultante_id' => 2
            ],
            [
                'concessionaria_id' => $idEnergisa, 'prioridade' => 99, 'descricao' => 'Individual Padrão (Fallback)',
                'variavel' => 'padrao', 'condicao' => '=', 'valor_min' => 'true', 'valor_max' => null, 'norma_resultante_id' => 1
            ]
        ];
        $this->db->table('regras_normas')->insertBatch($regrasNormas);

        // ==========================================================
        // 3. CRIAÇÃO DOS KITS (Necessário para ter IDs válidos)
        // ==========================================================
        
        // Kit 1: Poste 7m (Para travessia de rua)
        $this->db->table('kits')->insert([
            'nome' => 'Kit Infraestrutura Poste 7m (Travessia)',
            'slug' => 'infra-poste-7m'
        ]);
        $idKit7m = $this->db->insertID(); // Pega o ID que acabou de ser gerado (Ex: 1)

        // Kit 2: Poste 5m (Padrão)
        $this->db->table('kits')->insert([
            'nome' => 'Kit Infraestrutura Poste 5m (Padrão)',
            'slug' => 'infra-poste-5m'
        ]);
        $idKit5m = $this->db->insertID(); // Pega o ID (Ex: 2)

        // ==========================================================
        // 4. REGRAS DE MATERIAIS (Agora usando os IDs reais)
        // ==========================================================
        
        $regrasMateriais = [
            // PRIORIDADE 1: Se for RUA, usa o Kit 7m
            [
                'concessionaria_id' => $idEnergisa,
                'tipo_kit'          => 'infra', 
                'prioridade'        => 1,       
                'descricao'         => 'Travessia de Rua exige Poste 7m',
                'variavel'          => 'travessia',
                'condicao'          => '=',
                'valor_min'         => 'rua',
                'valor_max'         => null,
                'kit_id'            => $idKit7m // Usa o ID gerado acima
            ],
            // PRIORIDADE 99: Se não cair em nada, usa o Kit 5m
            [
                'concessionaria_id' => $idEnergisa,
                'tipo_kit'          => 'infra',
                'prioridade'        => 99,      
                'descricao'         => 'Infraestrutura Padrão (Poste 5m)',
                'variavel'          => 'padrao',
                'condicao'          => '=',
                'valor_min'         => 'true',
                'valor_max'         => null,
                'kit_id'            => $idKit5m // Usa o ID gerado acima
            ]
        ];

        $this->db->table('regras_materiais')->insertBatch($regrasMateriais);
    }
}