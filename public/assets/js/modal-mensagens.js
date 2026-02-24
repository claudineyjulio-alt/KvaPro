document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById('MensagensModal');

    // SEGURANÇA: Se o modal não existir (ex: erro no include), para o script aqui.
    if (!modal) return;

    // Função para fechar o modal
    window.closeMensagensModal = function() {
        modal.classList.remove('active');
    };

    // FUNÇÃO TURBINADA PARA CHAMADA VIA JS
    // Tipos aceitos: 'success', 'warning', 'error'
    window.showModalMessagens = function(tipo, titulo, mensagem) {
        // 1. Dicionário de estilos (Igual ao seu PHP)
        const estilos = {
            'success': { icon: 'fa-check-circle', color: '#2ecc71', btnClass: 'btn-close-modal-success' },
            'warning': { icon: 'fa-exclamation-circle', color: '#f1c40f', btnClass: 'btn-close-modal-warning' },
            'error':   { icon: 'fa-exclamation-triangle', color: '#ff6b6b', btnClass: 'btn-close-modal-error' }
        };

        // Usa o estilo passado ou cai no 'warning' por padrão se errar o nome
        const config = estilos[tipo] || estilos['warning'];

        // 2. Pega os elementos dentro do modal que precisamos alterar
        const iconeEl = modal.querySelector('.modal-icon-side i');
        const tituloEl = modal.querySelector('.modal-title');
        const mensagemEl = modal.querySelector('.modal-message');
        const botaoEl = modal.querySelector('.btn-close-modal');

        // 3. Atualiza os dados no HTML
        iconeEl.className = `fas ${config.icon} fa-5x`;
        iconeEl.style.color = config.color;
        
        tituloEl.textContent = titulo;
        mensagemEl.textContent = mensagem;
        
        // Atualiza a classe do botão mantendo a classe base
        botaoEl.className = `btn-close-modal ${config.btnClass}`;

        // 4. Exibe o modal
        modal.classList.add('active');
    };

    // Mantém a verificação original do PHP (Flashdata) ao carregar a página
    if (modal.dataset.ativo === "true") {
        setTimeout(() => {
            modal.classList.add('active');
        }, 100);
    }
});



// document.addEventListener("DOMContentLoaded", function () {
    
//     const modal = document.getElementById('MensagensModal');

//     // SEGURANÇA: Se o modal não existir (ex: erro no include), para o script aqui.
//     if (!modal) return;

//     // Precisamos usar 'window.nomeDaFuncao' para que o onclick="" do HTML funcione
//     window.closeMensagensModal = function() {
//         modal.classList.remove('active');
//     };

//     window.showModalMessagens = function(message) {
//         modal.classList.add('active');
//     };

//     if (modal.dataset.ativo === "true") {
//         // O setTimeout é importante para garantir que a transição CSS (fade in) funcione
//         setTimeout(() => {
//             modal.classList.add('active');
//         }, 100);
//     }

// });