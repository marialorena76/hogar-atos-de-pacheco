<?php

$Nome		= $_POST["name"];	// Pega o valor do campo Nome
//$Fone		= $_POST["Fone"];	// Pega o valor do campo Telefone
$Email		= $_POST["email"];	// Pega o valor do campo Email
$Mensagem	= $_POST["message"];	// Pega os valores do campo Mensagem
$assunt         = $_POST["subject"];	// Pega os valores do campo Assunto

//TEST CONFIG
/*
$Nome		= "AGUS";	// Pega o valor do campo Nome
//$Fone		= $_POST["Fone"];	// Pega o valor do campo Telefone
$Email		= "agusddt@gmail.com";	// Pega o valor do campo Email
$Mensagem	= "test";	// Pega os valores do campo Mensagem
$assunt         = "asunto test";	// Pega os valores do campo Assunto
*/


// Variável que junta os valores acima e monta o corpo do email

$Vai 		= "Nome: $Nome\n\nE-mail: $Email\n\nMensagem: $Mensagem\n";

// ----- CUERPO DEL EMAIL HTML
// form field names and their translations.
// array variable name => Text to appear in the email
$fields = array('name' => 'Nombre:', 'surname' => 'Apellido:', 'phone' => 'Telefono:', 'email' => 'Email:', 'message' => 'Mensaje:');

$emailTextHtml = "<h1>Tiene un nuevo mensaje de su formulario de contacto.</h1><hr>";
$emailTextHtml .= "<table>";
//print_r($_POST);
foreach ($_POST as $key => $value) {
    // If the field exists in the $fields array, include it in the email
    if (isset($fields[$key])) {
        $emailTextHtml .= "<tr><th>$fields[$key]</th><td>$value</td></tr>";
    }
}
$emailTextHtml .= "</table><hr>";
$emailTextHtml .= "<p>Hoy puede ser un gran día!</p>";
 
$Vai = $emailTextHtml;

// ----- FIN CUERPO DEL EMAIL HTML

//require_once("phpmailer/class.phpmailer.php");
//require 'phpmailer/PHPMailerAutoload.php';

require("class.phpmailer.php");
require("class.smtp.php");
 

define('GUSER', 'admin@hogaraltosdepacheco.com.ar');	// <-- Insira aqui o seu email
define('GPWD', 'mlvOIM*8rI');		// <-- Insira aqui a senha do seu email




function smtpmailer($para, $de, $de_nome, $assunto, $corpo) { 
	global $error;
	$mail = new PHPMailer();
	$mail->IsSMTP();		// Ativar SMTP
	$mail->SMTPDebug = 0;		// Debugar: 1 = erros e mensagens, 2 = mensagens apenas
	$mail->SMTPAuth = true;		// Autenticação ativada
	$mail->SMTPSecure = 'ssl';	// SSL REQUERIDO pelo email
	$mail->Port = 465;  		// A porta 587 deverá estar aberta em seu servidor
        $mail->isHTML(true);
        $mail->CharSet = "utf-8";

        // Datos de la cuenta de correo utilizada para enviar vía SMTP
        $smtpHost = "hogar00.ferozo.com";  // Dominio alternativo brindado en el email de alta 
        $smtpUsuario = "admin@hogaraltosdepacheco.com.ar";  // Mi cuenta de correo
        $smtpClave = "mlvOIM*8rI";  // Mi contraseña
        
        // Email donde se enviaran los datos cargados en el formulario de contacto
        $emailDestino = "info@hogaraltosdepacheco.com.ar";


        // VALORES A MODIFICAR //
        $mail->Host = $smtpHost; 
        $mail->Username = $smtpUsuario; 
        $mail->Password = $smtpClave;


        $mail->From = $de; // Email desde donde envío el correo.
        $mail->FromName = $de_nome;
        $mail->AddAddress($emailDestino); // Esta es la dirección a donde enviamos los datos del formulario


	$mail->Subject = $assunto;
        $mail->Body = $corpo;
        
        $mail->AltBody = "{$corpo}"; // Texto sin formato HTML
        //$mail->AddReplyTo($de, utf8_decode($de_nome));
        //$mail->addCC($para);
        $mail->addCC('estefia@yahoo.com');

        
	if(!$mail->Send()) {
		$error = 'Mail error: '.$mail->ErrorInfo; 
		return false;
	} else {
		$error = 'sent';
		return true;
	}
}



// Insira abaixo o email que irá receber a mensagem, o email que irá enviar (o mesmo da variável GUSER), o nome do email que envia a mensagem, o Assunto da mensagem e por último a variável com o corpo do email.

//if (smtpmailer('admin@hogaraltosdepachecho.com.ar', $Email, $Nome, 'Hogar Altos de Pacheco | Web - '.$assunt, $Vai)) {
if (smtpmailer('info@hogaraltosdepacheco.com.ar', $Email, $Nome, 'Hogar Altos de Pacheco | Web - '.$assunt, $Vai)) {

	 // Redireciona para uma página de obrigado.

}
if (!empty($error)) echo $error;

?>