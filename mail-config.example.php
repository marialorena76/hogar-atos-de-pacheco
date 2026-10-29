<?php
/*
 * Plantilla de configuración del correo.
 *
 * En el hosting, esta plantilla se sube con el nombre  mail-config.php  y se completa
 * con las claves reales. Ese archivo (mail-config.php) está en .gitignore: NUNCA va a GitHub.
 *
 * 'hosts' se prueban en orden. "localhost" es el servidor de correo del propio hosting.
 */
return array(

    // Formulario de Altos de Pacheco (contact.html)
    'pacheco' => array(
        'hosts'  => array('localhost', 'mail.hogaraltosdepacheco.com.ar'),
        'user'   => 'info@hogaraltosdepacheco.com.ar',
        'pass'   => 'PEGAR_AQUI_LA_CLAVE_DE_ESTA_CASILLA',
        'to'     => 'info@hogaraltosdepacheco.com.ar',
        'cc'     => '',   // opcional: otra dirección que reciba copia
        'prefix' => 'Hogar Altos de Pacheco | Web - ',
    ),

    // Formulario de Altos del Valle (home y valle.html)
    'valle' => array(
        'hosts'  => array('localhost', 'mail.hogaraltosdelvalle.com.ar'),
        'user'   => 'info@hogaraltosdelvalle.com.ar',
        'pass'   => 'PEGAR_AQUI_LA_CLAVE_DE_ESTA_CASILLA',
        'to'     => 'info@hogaraltosdelvalle.com.ar',
        'cc'     => '',
        'prefix' => 'Hogar Altos del Valle | Web - ',
    ),
);
