document.addEventListener("DOMContentLoaded", function () {
    
    const modal = document.getElementById('MensagensModal');

    // SEGURANÇA: Se o modal não existir (ex: erro no include), para o script aqui.
    if (!modal) return;

    // Precisamos usar 'window.nomeDaFuncao' para que o onclick="" do HTML funcione
    window.closeMensagensModal = function() {
        modal.classList.remove('active');
    };

    window.showModalMessagens = function(message) {
        modal.classList.add('active');
    };

    if (modal.dataset.ativo === "true") {
        // O setTimeout é importante para garantir que a transição CSS (fade in) funcione
        setTimeout(() => {
            modal.classList.add('active');
        }, 100);
    }

});