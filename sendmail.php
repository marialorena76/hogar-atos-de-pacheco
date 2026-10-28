<?php
// Suppress PHP errors from leaking into the AJAX response
ini_set('display_errors', 0);
error_reporting(0);
// Clean any output buffers so stray errors don't corrupt the response
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

function smtpmailer($de, $de_nome, $assunto, $corpo) {
    global $error;
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPDebug  = 0;
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;
    $mail->isHTML(true);
    $mail->CharSet    = 'utf-8';

    $mail->Host     = 'hogar00.ferozo.com';
    $mail->Username = 'admin@hogaraltosdepacheco.com.ar';
    $mail->Password = 'mlvOIM*8rI';

    $mail->From     = $de;
    $mail->FromName = $de_nome;
    $mail->AddAddress('info@hogaraltosdepacheco.com.ar');
    $mail->addCC('estefia@yahoo.com');

    $mail->Subject = $assunto;
    $mail->Body    = $corpo;
    $mail->AltBody = strip_tags($corpo);

    if (!$mail->Send()) {
        $error = 'error: ' . $mail->ErrorInfo;
        return false;
    }
    $error = 'sent';
    return true;
}

smtpmailer($Email, $Nome, 'Hogar Altos de Pacheco | Web - ' . $assunt, $emailTextHtml);

ob_end_clean();
echo $error;
?>