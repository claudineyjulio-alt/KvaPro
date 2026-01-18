<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EletCAD</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
          theme: {
            extend: {
              colors: {
                eletred: { DEFAULT: '#D62828', dark: '#A81818', light: '#FEE2E2' },
                eletgray: '#333333'
              },
              fontFamily: { sans: ['Inter', 'sans-serif'] }
            }
          }
        }
    </script>
    
    <style>
        .bg-grid-slate-200 { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32' width='32' height='32' fill='none' stroke='rgb(203 213 225 / 0.6)'%3E%3Cpath d='M0 .5H31.5V32'/%3E%3C/svg%3E"); }
    </style>
</head>
<body class="bg-gray-50 font-sans text-eletgray bg-grid-slate-200 min-h-screen flex flex-col items-center justify-center p-4">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 md:p-10 border border-gray-100 relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-eletred to-eletred-dark"></div>

        <div class="flex flex-col items-center mb-8">
            <div class="w-24 h-24 mb-4 relative hover:scale-105 transition-transform duration-300">
                <img src="<?= base_url('assets/img/logo.png') ?>" alt="EletCAD Logo" class="w-full h-full object-contain drop-shadow-lg">
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight text-eletgray">EletCAD</h1>
            <p class="text-sm text-gray-500 font-medium mt-1 uppercase tracking-wide">Painéis & Automação</p>
        </div>

        <div class="flex flex-col items-center space-y-4">
            <p class="text-center text-gray-600 text-sm mb-2">Acesse sua conta para continuar</p>
            
            <div id="buttonDiv"></div>
            
            <div id="error-msg" class="hidden text-red-600 text-sm bg-red-50 p-2 rounded w-full text-center border border-red-200"></div>
        </div>

        <div class="my-8 flex items-center">
            <div class="flex-grow border-t border-gray-200"></div>
            <span class="flex-shrink-0 mx-4 text-gray-300 text-xs">SISTEMA SEGURO</span>
            <div class="flex-grow border-t border-gray-200"></div>
        </div>

        <div class="text-center">
            <p class="text-xs text-gray-400">
                &copy; <?= date('Y') ?> EletCAD. Todos os direitos reservados.
            </p>
            <div class="mt-4 flex justify-center items-center gap-2 opacity-60 grayscale hover:grayscale-0 transition-all duration-500">
                 <span class="text-[10px] text-gray-400">Powered by</span>
                 <img src="<?= base_url('assets/img/logoElet.png') ?>" alt="Elet Elétrica" class="h-4">
            </div>
        </div>
    </div>

    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
        // Função chamada quando o Google devolve o token
        function handleCredentialResponse(response) {
            // Mostra carregando...
            const btnDiv = document.getElementById('buttonDiv');
            btnDiv.innerHTML = '<span class="text-eletred font-semibold animate-pulse">Validando acesso...</span>';

            // Envia para o seu Backend via AJAX (POST)
            fetch("<?= base_url('login/callback') ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: "credential=" + response.credential
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Sucesso! Redireciona para o dashboard
                    window.location.href = data.redirectUrl;
                } else {
                    // Erro do backend
                    showError("Erro: " + (data.error || "Falha no login"));
                    renderGoogleButton(); // Redesenha o botão
                }
            })
            .catch(err => {
                showError("Erro de conexão com o servidor.");
                console.error(err);
                renderGoogleButton();
            });
        }

        function showError(msg) {
            const errDiv = document.getElementById('error-msg');
            errDiv.innerText = msg;
            errDiv.classList.remove('hidden');
        }

        // Inicializa o Google
        window.onload = function () {
            google.accounts.id.initialize({
                client_id: "<?= getenv('GOOGLE_CLIENT_ID') ?>",
                callback: handleCredentialResponse,
                auto_select: false, // Não tenta logar automático se já autorizou antes
                cancel_on_tap_outside: false
            });

            // Renderiza o botão no estilo novo
            renderGoogleButton();

            // Ativa o One Tap (o popup no topo)
            google.accounts.id.prompt((notification) => {
                if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
                    console.log("One Tap não exibido/pulado");
                }
            });
        };

        function renderGoogleButton() {
            google.accounts.id.renderButton(
                document.getElementById("buttonDiv"),
                { 
                    theme: "outline", 
                    size: "large", 
                    width: "280", // Largura do botão
                    text: "signin_with",
                    shape: "pill",
                    logo_alignment: "left"
                }
            );
        }
    </script>
</body>
</html>