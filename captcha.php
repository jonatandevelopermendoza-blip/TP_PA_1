<?php
require_once 'config.php';

$caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$longitud = 6;
$codigo = '';

for ($i = 0; $i < $longitud; $i++) {
    $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
}

$_SESSION['captcha_codigo'] = $codigo;
$_SESSION['captcha_tiempo'] = time();

header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

$ancho = 180;
$alto  = 50;

$imagen = imagecreatetruecolor($ancho, $alto);

$fondo  = imagecolorallocate($imagen, 240, 244, 248);
$texto  = imagecolorallocate($imagen, 13, 71, 110);
$linea  = imagecolorallocate($imagen, 150, 180, 210);
$punto  = imagecolorallocate($imagen, 100, 130, 160);

imagefilledrectangle($imagen, 0, 0, $ancho, $alto, $fondo);

for ($i = 0; $i < 6; $i++) {
    imageline($imagen,
        random_int(0, $ancho), random_int(0, $alto),
        random_int(0, $ancho), random_int(0, $alto),
        $linea);
}

for ($i = 0; $i < 400; $i++) {
    imagesetpixel($imagen, random_int(0, $ancho), random_int(0, $alto), $punto);
}

$fuente = 5;
$anchoTexto = imagefontwidth($fuente) * $longitud;
$posX = ($ancho - $anchoTexto) / 2;
$posY = ($alto - imagefontheight($fuente)) / 2;

for ($i = 0; $i < $longitud; $i++) {
    $offsetY = random_int(-5, 5);
    imagestring($imagen, $fuente,
        $posX + ($i * imagefontwidth($fuente)),
        $posY + $offsetY,
        $codigo[$i], $texto);
}

imagepng($imagen);
imagedestroy($imagen);
exit;
?>