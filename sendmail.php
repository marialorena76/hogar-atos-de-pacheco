<?php
// Suppress PHP errors from leaking into the AJAX response
ini_set('display_errors', 0);
error_reporting(0);
ob_start();

$Nome     = isset($_POST["name"])    ? strip_tags(trim($_POST["name"]))    : '';
$Email    = isset($_POST["email"])   ? strip_tags(trim($_POST["email"]))   : '';
$Mensagem = isset($_POST["message"]) ? strip_tags(trim($_POST["message"])) : '';
$assunt   = isset($_POST["subject"]) ? strip_tags(trim($_POST["subject"])) : 'Consulta desde el sitio web';

if (empty($Nome) || empty($Email) || empty($Mensagem)) {
    ob_end_clean();
    echo 'error';
    exit;
}

// Detectar qué formulario envió (Altos del Valle vs Altos de Pacheco)
$esValle = (strpos($assunt, '[Altos del Valle]') !== false);

if ($esValle) {
    $smtpHost     = 'mail.hogaraltosdelvalle.com.ar';
    $smtpUsuario  = 'info@altosdelvalle.com.ar';
    $smtpClave    = 'B4$v=)pqOM.xRTgU';
    $emailDestino = 'info@altosdelvalle.com.ar';
    $prefijo      = 'Hogar Altos del Valle | Web - ';
} else {
    $smtpHost     = 'mail.hogaraltosdepacheco.com.ar';
    $smtpUsuario  = 'info@hogaraltosdepacheco.com.ar';
    $smtpClave    = 'jZdqLoJ,f{';
    $emailDestino = 'info@hogaraltosdepacheco.com.ar';
    $prefijo      = 'Hogar Altos de Pacheco | Web - ';
}

$fields = array(
    'name'    => 'Nombre:',
    'email'   => 'Email:',
    'subject' => 'Asunto:',
    'message' => 'Mensaje:',
);

$emailTextHtml  = "<h1>Nuevo mensaje desde el formulario de contacto.</h1><hr>";
$emailTextHtml .= "<table cellpadding='6' cellspacing='0'>";
foreach ($_POST as $key => $value) {
    if (isset($fields[$key])) {
        $emailTextHtml .= "<tr><th style='text-align:left;padding-right:12px'>{$fields[$key]}</th><td>" . htmlspecialchars($value) . "</td></tr>";
    }
}
$emailTextHtml .= "</table><hr>";

require_once("class.phpmailer.php");
require_once("class.smtp.php");

$error = '';

function smtpmailer($smtpHost, $smtpUsuario, $smtpClave, $emailDestino, $de, $de_nome, $asunto, $corpo) {
    global $error;
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPDebug  = 0;
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;
    $mail->isHTML(true);
    $mail->CharSet    = 'utf-8';

    $mail->Host     = $smtpHost;
    $mail->Username = $smtpUsuario;
    $mail->Password = $smtpClave;

    $mail->From     = $smtpUsuario;
    $mail->FromName = $de_nome;
    $mail->AddAddress($emailDestino);
    $mail->addReplyTo($de, $de_nome);

    $mail->Subject = $asunto;
    $mail->Body    = $corpo;
    $mail->AltBody = strip_tags($corpo);

    if (!$mail->Send()) {
        $error = 'error: ' . $mail->ErrorInfo;
        return false;
    }
    $error = 'sent';
    return true;
}

smtpmailer($smtpHost, $smtpUsuario, $smtpClave, $emailDestino, $Email, $Nome, $prefijo . $assunt, $emailTextHtml);

ob_end_clean();
echo $error;
?>