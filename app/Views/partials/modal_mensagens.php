    <link rel="stylesheet" href="<?= base_url('assets/css/modal-mensagens.css') ?>">
    <script src="<?= base_url('assets/js/modal-mensagens.js') ?>"></script>
    <?php
    $modalData = [
        'class' => 'btn-close-modal-success',
        'title' => 'MODAL PADRÃO',
        'msg'   => 'Nenhuma mensagem para exibir.',
        'icon'  => 'fa-check-circle',
        'color' => '#2ecc71',
        'ativo' => false
    ];


    if (session()->getFlashdata('success')) {
        $modalData = [
            'class' => 'btn-close-modal-success',
            'title' => 'SUCESSO!',
            'msg'   => session()->getFlashdata('success'),
            'icon'  => 'fa-check-circle',
            'color' => '#2ecc71',
            'ativo' => true
        ];
    } elseif (session()->getFlashdata('warning')) {
        $modalData = [
            'class' => 'btn-close-modal-warning',
            'title' => 'ATENÇÃO!',
            'msg'   => session()->getFlashdata('warning'),
            'icon'  => 'fa-exclamation-circle',
            'color' => '#f1c40f',
            'ativo' => true
        ];
    } elseif (session()->getFlashdata('error')) {
        $modalData = [
            'class' => 'btn-close-modal-error',
            'title' => 'ERRO!',
            'msg'   => session()->getFlashdata('error'),
            'icon'  => 'fa-exclamation-triangle',
            'color' => '#ff6b6b',
            'ativo' => true
        ];
    }
    ?>

    <div id="MensagensModal" class="modal-overlay" data-ativo="<?= $modalData['ativo'] ? 'true' : 'false' ?>">
        <div class="modal-box">
            <div class="modal-icon-side">
                <i class="fas <?= $modalData['icon']; ?> fa-5x" style="color: <?= $modalData['color']; ?>"></i>
            </div>
            <div class="modal-content-side">
                <h3 class="modal-title"><?= $modalData['title']; ?></h3>
                <p class="modal-message"><?= $modalData['msg']; ?></p>
                <button class="btn-close-modal <?= $modalData['class']; ?> " onclick="closeMensagensModal()">FECHAR</button>
            </div>
        </div>
    </div>