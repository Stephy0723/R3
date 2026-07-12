<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

$data = $_POST;  

$mensaje = "📩 NUEVA SOLICITUD DE CRÉDITO\n";
$mensaje .= "---------------------------------------\n";
$mensaje .= "🧍 DATOS PERSONALES\n";
$mensaje .= "Nombre completo: " . ($data['nombre'] ?? '') . "\n";
$mensaje .= "Cédula: " . ($data['cedula'] ?? '') . "\n";
$mensaje .= "Apodo: " . ($data['apodo'] ?? '') . "\n";
$mensaje .= "Sexo: " . ($data['sexo'] ?? '') . "\n";
$mensaje .= "Edad: " . ($data['edad'] ?? '') . "\n";
$mensaje .= "Ciudad: " . ($data['ciudad'] ?? '') . "\n";
$mensaje .= "Estado civil: " . ($data['estado_civil'] ?? '') . "\n";
$mensaje .= "Tipo de vivienda: " . ($data['vivienda'] ?? '') . "\n";
$mensaje .= "Dirección: " . ($data['direccion'] ?? '') . "\n";
$mensaje .= "Casa No.: " . ($data['casa'] ?? '') . "\n";
$mensaje .= "Sector: " . ($data['sector'] ?? '') . "\n";
$mensaje .= "Tel. Casa: " . ($data['telcasa'] ?? '') . "\n";
$mensaje .= "Celular: " . ($data['celular'] ?? '') . "\n\n";

$mensaje .= "💼 INFORMACIÓN LABORAL\n";
$mensaje .= "Empresa: " . ($data['empresa'] ?? '') . "\n";
$mensaje .= "Cargo: " . ($data['cargo'] ?? '') . "\n";
$mensaje .= "Dirección de trabajo: " . ($data['dirtrabajo'] ?? '') . "\n";
$mensaje .= "Teléfono trabajo: " . ($data['teltrabajo'] ?? '') . "\n";
$mensaje .= "Tiempo en la empresa: " . ($data['tiempoempresa'] ?? '') . "\n";
$mensaje .= "Ingresos mensuales: " . ($data['ingresos'] ?? '') . "\n\n";

$mensaje .= "👥 REFERENCIAS PERSONALES\n";
$mensaje .= "Referencia 1: " . ($data['ref1'] ?? '-') . " - " . ($data['tel1'] ?? '-') . "\n";
$mensaje .= "Referencia 2: " . ($data['ref2'] ?? '-') . " - " . ($data['tel2'] ?? '-') . "\n";
$mensaje .= "Referencia 3: " . ($data['ref3'] ?? '-') . " - " . ($data['tel3'] ?? '-') . "\n\n";

$mensaje .= "💸 DATOS DEL PRÉSTAMO\n";
$mensaje .= "Monto solicitado: " . ($data['monto'] ?? '') . "\n";
$mensaje .= "Tiempo (meses): " . ($data['tiempo_prestamo'] ?? '') . "\n";
$mensaje .= "Observaciones: " . ($data['observaciones'] ?? '-') . "\n";
$mensaje .= "---------------------------------------\n";
$mensaje .= "🕒 Fecha de envío: " . date("Y-m-d H:i:s") . "\n";

// ENVÍO POR SMTP
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'salylopez0723@gmail.com'; 
    $mail->Password = 'bgdc wcqh kzgk rser';     // ← ese obviamente ya no es 
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('salylopez0723@gmail.com', 'Formulario Inversiones 3R');
    $mail->addAddress('stephanielopezfrias@gmail.com');

    $mail->isHTML(false);
    $mail->Subject = 'Nueva Solicitud de Crédito - Inversiones 3R';
    $mail->Body    = $mensaje;

    $mail->send();
    echo "<h2 style='color: green;'>Solicitud enviada correctamente </h2>";
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error al enviar el formulario <br>{$mail->ErrorInfo}</h2>";
}
?>
