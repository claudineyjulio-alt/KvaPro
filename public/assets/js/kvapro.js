const sidebar = document.getElementById('sidebar');
const wrapper = document.getElementById('mainWrapper');
const overlay = document.getElementById('overlay');

function handleSidebarToggle() {
    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('mobile-active');
        overlay.classList.toggle('active');
    } else {
        sidebar.classList.toggle('collapsed');
        wrapper.classList.toggle('expanded');
    }
}

function closeMobileSidebar() {
    sidebar.classList.remove('mobile-active');
    overlay.classList.remove('active');
}
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) {
        sidebar.classList.remove('mobile-active');
        overlay.classList.remove('active');
    }
});


// Função para gerar o Grid Mobile automaticamente
function generateMobileGrid() {
    const sidebarLinks = document.querySelectorAll('#sidebar .nav-links .nav-item');
    const mobileContainer = document.getElementById('mobileQuickAccess');

    mobileContainer.innerHTML = '';

    sidebarLinks.forEach(link => {
        const newLink = document.createElement('a');
        newLink.href = link.href;

        // Copia eventos de clique (ex: modais)
        if (link.getAttribute('onclick')) {
            newLink.setAttribute('onclick', link.getAttribute('onclick'));
        }

        // Adiciona a classe base
        newLink.classList.add('mobile-grid-item');

        // --- AQUI ESTÁ A MÁGICA ---
        // Se o item original do menu estiver ativo, ativa o do grid também
        if (link.classList.contains('active')) {
            newLink.classList.add('active');
        }
        // --------------------------

        // Clona ícone e pega texto
        const icon = link.querySelector('i').cloneNode(true);
        const text = link.querySelector('span').innerText;

        const textSpan = document.createElement('span');
        textSpan.innerText = text;

        newLink.appendChild(icon);
        //newLink.appendChild(textSpan);

        mobileContainer.appendChild(newLink);
    });
}

// Executa a função assim que o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    generateMobileGrid();
});