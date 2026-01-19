<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?> - KvaPro</title>
    
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
          theme: {
            extend: {
              colors: {
                eletblue: { DEFAULT: '#0f2649', dark: '#0a1a33', light: '#eef2ff' },
                eletgray: '#333333'
              },
              fontFamily: { sans: ['Inter', 'sans-serif'] }
            }
          }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans text-eletgray min-h-screen py-8 px-4 flex flex-col justify-between">

    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100 w-full relative">
        
        <?php if ($is_editing): ?>
        <a href="<?= base_url('dashboard') ?>" class="absolute top-4 left-4 z-10 text-white/80 hover:text-white flex items-center gap-1 text-sm bg-white/10 px-3 py-1 rounded-full hover:bg-white/20 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Voltar
        </a>
        <?php endif; ?>

        <div class="bg-eletblue p-8 flex flex-col md:flex-row items-center justify-between gap-8 pt-12 md:pt-8">
            <div class="text-white text-center md:text-left order-2 md:order-1 flex-1">
                <h1 class="text-3xl font-bold"><?= $titulo ?></h1>
                <p class="text-base opacity-80 mt-2 leading-relaxed"><?= $subtitulo ?></p>
            </div>
            
            <div class="order-1 md:order-2 flex-shrink-0">
                <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo KvaPro" 
                     class="h-40 md:h-60 w-auto object-contain drop-shadow-2xl hover:scale-105 transition-transform duration-500">
            </div>
        </div>

        <?php if (session()->getFlashdata('msg')): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 m-8 mb-0" role="alert">
                <p><?= session()->getFlashdata('msg') ?></p>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('cadastro/salvar') ?>" method="POST" class="p-8 md:p-10 space-y-8">
            <?= csrf_field() ?>
            <input type="hidden" name="is_editing" value="<?= $is_editing ? '1' : '0' ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo *</label>
                    <input type="text" name="nome" value="<?= old('nome', $user['nome']) ?>" required 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-eletblue focus:border-eletblue p-3 border">
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-400 mb-1">Email de Login</label>
                    <input type="email" value="<?= $user['email'] ?>" disabled 
                        class="w-full bg-gray-100 border-gray-200 text-gray-500 rounded-lg p-3 border cursor-not-allowed">
                </div>
                
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email de Contato (se diferente)</label>
                    <input type="email" name="email_contato" value="<?= old('email_contato', $user['email']) ?>" 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-eletblue p-3 border">
                </div>
            </div>

            <div class="border-t border-gray-100"></div>

            <div>
                <h3 class="text-lg font-semibold text-gray-700 flex items-center gap-2 mb-4">
                    Perfil Profissional
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Eu sou: *</label>
                        <select name="tipo_profissional" id="tipo_profissional" onchange="toggleForm()" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-eletblue p-3 border bg-white">
                            <option value="">Selecione...</option>
                            <option value="Eletricista" <?= ($user['tipo_profissional'] == 'Eletricista') ? 'selected' : '' ?>>Eletricista</option>
                            <option value="Tecnico" <?= ($user['tipo_profissional'] == 'Tecnico') ? 'selected' : '' ?>>Técnico</option>
                            <option value="Engenheiro" <?= ($user['tipo_profissional'] == 'Engenheiro') ? 'selected' : '' ?>>Engenheiro</option>
                            <option value="Arquiteto" <?= ($user['tipo_profissional'] == 'Arquiteto') ? 'selected' : '' ?>>Arquiteto</option>
                            <option value="Pedreiro" <?= ($user['tipo_profissional'] == 'Pedreiro') ? 'selected' : '' ?>>Pedreiro</option>
                            <option value="Vendedor" <?= ($user['tipo_profissional'] == 'Vendedor') ? 'selected' : '' ?>>Vendedor</option>
                            <option value="Concessionaria" <?= ($user['tipo_profissional'] == 'Concessionaria') ? 'selected' : '' ?>>Concessionária</option>
                            <option value="Cliente Final" <?= ($user['tipo_profissional'] == 'Cliente Final') ? 'selected' : '' ?>>Cliente Final</option>
                            <option value="Outros" <?= ($user['tipo_profissional'] == 'Outros') ? 'selected' : '' ?>>Outros</option>
                        </select>
                    </div>

                    <div id="div-area" class="col-span-2 md:col-span-1 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Área de Atuação: *</label>
                        <select name="area_atuacao" id="area_atuacao"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-eletblue p-3 border bg-white">
                            </select>
                        <input type="hidden" id="area_salva" value="<?= $user['area_atuacao'] ?>">
                    </div>

                    <div id="div-registro" class="col-span-2 hidden bg-gray-50 p-6 rounded-lg border border-gray-200 mt-2">
                        <div class="text-xs text-gray-500 font-bold uppercase tracking-wide mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-eletblue"></span> Dados do Registro Profissional
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Órgão</label>
                                <input type="text" name="registro_orgao" value="<?= old('registro_orgao', $user['registro_orgao']) ?>" class="w-full border-gray-300 rounded text-sm p-2 border">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">UF</label>
                                <select name="registro_uf" class="w-full border-gray-300 rounded text-sm p-2 border bg-white">
                                    <option value="MG" <?= ($user['registro_uf'] == 'MG') ? 'selected' : '' ?>>MG</option>
                                    <option value="SP" <?= ($user['registro_uf'] == 'SP') ? 'selected' : '' ?>>SP</option>
                                    <option value="RJ" <?= ($user['registro_uf'] == 'RJ') ? 'selected' : '' ?>>RJ</option>
                                    <option value="ES" <?= ($user['registro_uf'] == 'ES') ? 'selected' : '' ?>>ES</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Número</label>
                                <input type="text" name="registro_numero" value="<?= old('registro_numero', $user['registro_numero']) ?>" class="w-full border-gray-300 rounded text-sm p-2 border">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100"></div>

            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Dados da Empresa <span class="text-sm font-normal text-gray-400">(Opcional)</span></h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Razão Social</label>
                        <input type="text" name="razao_social" value="<?= old('razao_social', $user['razao_social']) ?>" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-eletblue p-3 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label>
                        <input type="text" name="cnpj" maxlength="18" placeholder="00.000.000/0000-00"
                            value="<?= old('cnpj', $user['cnpj']) ?>" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-eletblue p-3 border">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefone / WhatsApp</label>
                        <input type="text" name="telefone" maxlength="15" placeholder="(00) 00000-0000"
                            value="<?= old('telefone', $user['telefone']) ?>" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-eletblue p-3 border">
                    </div>
                </div>
            </div>

            <?php if (!$is_editing): ?>
            <div class="bg-blue-50 p-5 rounded-lg border border-blue-100">
                <label class="flex items-start space-x-3 cursor-pointer">
                    <input type="checkbox" name="termos" required class="mt-1 h-5 w-5 text-eletblue border-gray-300 rounded focus:ring-eletblue">
                    <span class="text-sm text-gray-700 leading-snug">
                        Aceito os <a href="#" class="text-eletblue underline font-bold">Termos de Uso</a>...
                    </span>
                </label>
            </div>
            <?php endif; ?>

            <button type="submit" class="w-full bg-eletblue hover:bg-eletblue-dark text-white font-bold text-lg py-4 px-6 rounded-xl shadow-lg transition duration-300 transform hover:-translate-y-1 hover:shadow-2xl">
                <?= $botao_texto ?>
            </button>
        </form>
    </div>

    <footer class="mt-12 mb-6 max-w-4xl mx-auto w-full text-center">
            <div class="flex flex-col items-center justify-center gap-6">
                <p class="text-xs text-gray-400 font-bold tracking-widest uppercase">Desenvolvido por</p>
                
                <div class="flex items-center justify-center gap-10 grayscale hover:grayscale-0 transition-all duration-700 opacity-70 hover:opacity-100">
                    <div class="flex flex-col items-center gap-2 group cursor-pointer hover:scale-110 transition-transform">
                        <img src="<?= base_url('assets/img/logoElet.png') ?>" alt="Elet Elétrica" class="h-10 w-auto">
                        <span class="text-[10px] text-gray-500 opacity-0 group-hover:opacity-100 transition-opacity">Elet Elétrica</span>
                    </div>

                    <div class="h-10 w-px bg-gray-300"></div>

                    <div class="flex flex-col items-center gap-2 group cursor-pointer hover:scale-110 transition-transform">
                        <img src="<?= base_url('assets/img/logoEletcad.png') ?>" alt="EletCAD" class="h-10 w-auto">
                        <span class="text-[10px] text-gray-500 opacity-0 group-hover:opacity-100 transition-opacity">Plataforma EletCAD</span>
                    </div>
                </div>
                
                <p class="text-[11px] text-gray-400 mt-2">
                    &copy; <?= date('Y') ?> KvaPro - Módulo de Dimensionamento. Todos os direitos reservados.
                </p>
            </div>
        </footer>

<script>
        // 1. Função de Máscaras
        function aplicarMascaras() {
            const cnpjInput = document.querySelector('input[name="cnpj"]');
            const telInput = document.querySelector('input[name="telefone"]');

            // Máscara de CNPJ
            if (cnpjInput) {
                cnpjInput.addEventListener('input', function (e) {
                    let x = e.target.value.replace(/\D/g, '').substring(0, 14); // Apenas números
                    x = x.replace(/^(\d{2})(\d)/, '$1.$2');
                    x = x.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                    x = x.replace(/\.(\d{3})(\d)/, '.$1/$2');
                    x = x.replace(/(\d{4})(\d)/, '$1-$2');
                    e.target.value = x;
                });
            }

            // Máscara de Telefone (Híbrida: Fixo e Celular)
            if (telInput) {
                telInput.addEventListener('input', function (e) {
                    let x = e.target.value.replace(/\D/g, '').substring(0, 11); // Apenas números
                    x = x.replace(/^(\d{2})(\d)/, '($1) $2');
                    
                    if (x.length > 10) {
                        // Celular: (XX) XXXXX-XXXX
                        x = x.replace(/^(\(\d{2}\) \d{5})(\d)/, '$1-$2');
                    } else {
                        // Fixo: (XX) XXXX-XXXX
                        x = x.replace(/^(\(\d{2}\) \d{4})(\d)/, '$1-$2');
                    }
                    e.target.value = x;
                });
            }
        }

        // 2. Função Visual do Formulário (Já existia)
        function toggleForm() {
            const tipo = document.getElementById('tipo_profissional').value;
            const divRegistro = document.getElementById('div-registro');
            const divArea = document.getElementById('div-area');
            const selectArea = document.getElementById('area_atuacao');
            const areaSalva = document.getElementById('area_salva') ? document.getElementById('area_salva').value : '';

            const comRegistro = ['Tecnico', 'Engenheiro', 'Arquiteto'];
            if (comRegistro.includes(tipo)) {
                divRegistro.classList.remove('hidden');
                divRegistro.classList.add('grid');
            } else {
                divRegistro.classList.add('hidden');
                divRegistro.classList.remove('grid');
            }

            selectArea.innerHTML = '<option value="">Selecione a área...</option>';
            selectArea.removeAttribute('required');
            divArea.classList.add('hidden');

            let areas = [];
            if (tipo === 'Engenheiro') {
                areas = ['Eletricista', 'Civil', 'Mecânico', 'Automação', 'Eletrônica', 'Produção', 'Outros'];
            } else if (tipo === 'Tecnico') {
                areas = ['Eletrotécnica', 'Eletromecânica', 'Eletrônica', 'Edificações', 'Automação', 'Mecânica', 'Outros'];
            }

            if (areas.length > 0) {
                divArea.classList.remove('hidden');
                selectArea.setAttribute('required', 'required');
                areas.forEach(area => {
                    let isSelected = (area === areaSalva);
                    selectArea.add(new Option(area, area, isSelected, isSelected));
                });
            }
        }

        // 3. Inicialização ao Carregar a Página
        window.onload = function() {
            toggleForm();     // Ajusta os campos visuais
            aplicarMascaras(); // Ativa as máscaras
        };
    </script>
</body>
</html>