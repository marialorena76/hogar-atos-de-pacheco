<?php
/*
 * Envío de los formularios de contacto (Altos de Pacheco y Altos del Valle).
 *
 * IMPORTANTE: las claves de las casillas de correo NO van en este archivo.
 * Se leen de mail-config.php, que existe solo en el hosting y no se sube a GitHub
 * (ver mail-config.example.php).
 *
 * Responde siempre en texto plano: "sent" si se envió, "error" si no.
 * El detalle técnico de un fallo queda en el archivo error_log del hosting.
 */
ini_set('display_errors', 0);
error_reporting(0);
header('Content-Type: text/plain; charset=utf-8');

function responder($texto) {
    echo $texto;
    exit;
}

function limpiar($valor, $maximo) {
    $valor = trim(strip_tags((string) $valor));
    if (function_exists('mb_substr')) {
        return mb_substr($valor, 0, $maximo, 'UTF-8');
    }
    return substr($valor, 0, $maximo);
}

if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder('error');
}

// --- Configuración (claves) ---
$archivoConfig = dirname(__FILE__) . '/mail-config.php';
if (!is_file($archivoConfig)) {
    error_log('sendmail.php: falta el archivo mail-config.php');
    responder('error');
}
$config = require $archivoConfig;

// --- Datos del formulario ---
$nombre  = limpiar(isset($_POST['name'])    ? $_POST['name']    : '', 120);
$email   = limpiar(isset($_POST['email'])   ? $_POST['email']   : '', 160);
$asunto  = limpiar(isset($_POST['subject']) ? $_POST['subject'] : '', 200);
$mensaje = limpiar(isset($_POST['message']) ? $_POST['message'] : '', 5000);

if ($nombre === '' || $mensaje === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responder('error');
}

// --- ¿De qué sitio viene? El formulario de Altos del Valle marca el asunto con "[Altos del Valle]"
// y en ese formulario el campo "subject" es el teléfono.
$etiquetaValle = '[Altos del Valle]';
$esValle = (strpos($asunto, $etiquetaValle) !== false);
$cuenta = $esValle ? $config['valle'] : $config['pacheco'];

if ($esValle) {
    $telefono = trim(str_replace($etiquetaValle, '', $asunto));
    $asuntoMail = 'Consulta desde el sitio' . ($telefono !== '' ? ' - Tel. ' . $telefono : '');
} else {
    $telefono = '';
    $asuntoMail = ($asunto !== '') ? $asunto : 'Consulta desde el sitio web';
}

// --- Cuerpo del mail ---
$filas = array('Nombre:' => $nombre, 'Email:' => $email);
if ($esValle) {
    $filas['Teléfono:'] = ($telefono !== '') ? $telefono : '(no indicó)';
} else {
    $filas['Asunto:'] = $asuntoMail;
}
$filas['Mensaje:'] = $mensaje;

$cuerpo = '<h2>Nuevo mensaje desde el formulario de contacto</h2><hr>';
$cuerpo .= '<table cellpadding="6" cellspacing="0">';
foreach ($filas as $etiqueta => $valor) {
    $cuerpo .= '<tr><th style="text-align:left;vertical-align:top;padding-right:12px">' . $etiqueta
            . '</th><td>' . nl2br(htmlspecialchars($valor, ENT_QUOTES, 'UTF-8')) . '</td></tr>';
}
$cuerpo .= '</table><hr>';

// --- Envío por SMTP ---
require_once dirname(__FILE__) . '/class.phpmailer.php';
require_once dirname(__FILE__) . '/class.smtp.php';

$hosts = isset($cuenta['hosts']) ? $cuenta['hosts'] : array('localhost');
$enviado = false;

foreach ($hosts as $host) {
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPDebug  = 0;
    $mail->Timeout    = 15;
    $mail->Host       = $host;
    $mail->Port       = 465;
    $mail->SMTPSecure = 'ssl';
    $mail->SMTPAuth   = true;
    $mail->Username   = $cuenta['user'];
    $mail->Password   = $cuenta['pass'];
    $mail->CharSet    = 'utf-8';
    $mail->isHTML(true);

    // Dentro del mismo servidor ("localhost") el certificado no coincide con el nombre: no se verifica.
    // Para cualquier otro servidor sí se verifica el certificado.
    if ($host === 'localhost' || $host === '127.0.0.1') {
        $mail->SMTPOptions = array('ssl' => array(
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
        ));
    }

    $mail->From     = $cuenta['user'];
    $mail->FromName = 'Web ' . ($esValle ? 'Altos del Valle' : 'Altos de Pacheco');
    $mail->AddAddress($cuenta['to']);
    if (!empty($cuenta['cc'])) {
        $mail->addCC($cuenta['cc']);
    }
    $mail->addReplyTo($email, $nombre);

    $mail->Subject = $cuenta['prefix'] . $asuntoMail;
    $mail->Body    = $cuerpo;
    $mail->AltBody = strip_tags(str_replace(array('</tr>', '<br />'), "\n", $cuerpo));

    if ($mail->Send()) {
        $enviado = true;
        break;
    }
    error_log('sendmail.php: fallo con ' . $host . ' -> ' . $mail->ErrorInfo);
}

responder($enviado ? 'sent' : 'error');
