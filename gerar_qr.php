<?php
require_once 'phpqrcode/qrlib.php';

function gerarQRCode($dados, $nomeArquivo) {
    $caminho = 'imagens_qr/' . $nomeArquivo;
    if (!is_dir('imagens_qr')) {
        mkdir('imagens_qr');
    }
    QRcode::png($dados, $caminho, QR_ECLEVEL_H, 6);
    return $caminho;
}
?>
