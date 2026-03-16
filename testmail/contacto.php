<?php
/**
 * @version 1.0
 */

require("class.phpmailer.php");
require("class.smtp.php");

// Valores enviados desde el formulario
if ( !isset($_POST["name"]) || !isset($_POST["email"]) || !isset($_POST["subject"]) || !isset($_POST["message"]) ) {
    die ("Es necesario completar todos los datos del formulario");
}
$nombre = $_POST["name"];
$email = $_POST["email"];
$mensaje = $_POST["message"];
$asunto = $_POST["subject"];

// Datos de la cuenta de correo utilizada para enviar vía SMTP
$smtpHost = "hogar00.ferozo.com";  // Dominio alternativo brindado en el email de alta 
$smtpUsuario = "admin@hogaraltosdepacheco.com.ar";  // Mi cuenta de correo
$smtpClave = "mlvOIM*8rI";  // Mi contraseña

// Email donde se enviaran los datos cargados en el formulario de contacto
$emailDestino = "info@hogaraltosdepacheco.com.ar";



// ************************** CUERPO DEL EMAIL HTML   **************************

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

// ************************** FIN CUERPO DEL EMAIL HTML  **************************




$mail = new PHPMailer();
$mail->IsSMTP();
$mail->SMTPAuth = true;
$mail->Port = 465; 
$mail->SMTPSecure = 'ssl';
$mail->IsHTML(true); 
$mail->CharSet = "utf-8";


// VALORES A MODIFICAR //
$mail->Host = $smtpHost; 
$mail->Username = $smtpUsuario; 
$mail->Password = $smtpClave;

$mail->From = $email; // Email desde donde envío el correo.
$mail->FromName = $nombre;
$mail->AddAddress($emailDestino); // Esta es la dirección a donde enviamos los datos del formulario

$mail->Subject = 'Hogar Altos de Pacheco | Web - '.$asunto; // Este es el titulo del email.
//$mensajeHtml = nl2br($mensaje);
$mensajeHtml .= $emailTextHtml;
$mail->Body = "{$mensajeHtml}"; // Texto del email en formato HTML
$mail->AltBody = "{$mensaje} \n\n Formulario de ejemplo By Doltech"; // Texto sin formato HTML
// FIN - VALORES A MODIFICAR //

$estadoEnvio = $mail->Send(); 
if($estadoEnvio){
    echo "El correo fue enviado correctamente.";
} else {
    echo "Ocurrió un error inesperado.";
}
