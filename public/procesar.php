<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', '0');
error_reporting(E_ALL);
date_default_timezone_set('America/Santo_Domingo');

function input_value(array $data, string $key, string $fallback = ''): string
{
    return trim((string)($data[$key] ?? $fallback));
}

function respond(bool $ok, string $message, int $status = 200): void
{
    http_response_code($status);

    if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => $ok ? 'ok' : 'error',
            'message' => $message,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $color = $ok ? '#176b3a' : '#b42318';
    $title = $ok ? 'Solicitud enviada' : 'No se pudo enviar';
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    echo "<!doctype html><html lang='es'><meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'>";
    echo "<title>{$title} | Inversiones 3R</title>";
    echo "<body style='font-family:Arial,sans-serif;background:#f5f7fb;margin:0;padding:32px'>";
    echo "<main style='max-width:620px;margin:8vh auto;background:#fff;border-radius:12px;padding:32px;box-shadow:0 18px 45px rgba(15,23,42,.12)'>";
    echo "<h1 style='color:{$color};margin-top:0'>{$title}</h1>";
    echo "<p style='line-height:1.6;color:#334155'>{$safeMessage}</p>";
    echo "<a href='index.html' style='display:inline-block;margin-top:16px;color:#0f5f80;font-weight:700'>Volver al formulario</a>";
    echo "</main></body></html>";
    exit;
}

function mail_config(string $key, string $fallback = ''): string
{
    static $config = null;

    if ($config === null) {
        $configPath = dirname(__DIR__) . '/config/mail.php';
        $config = is_file($configPath) ? require $configPath : [];
    }

    $value = getenv($key);

    if ($value !== false && $value !== '') {
        return (string)$value;
    }

    return (string)($config[$key] ?? $fallback);
}

function h(array $data, string $key, string $fallback = ''): string
{
    return htmlspecialchars(input_value($data, $key, $fallback), ENT_QUOTES, 'UTF-8');
}

function email_section(string $title): string
{
    return '<tr><th colspan="2" style="background:#e9e9e9;border:1px solid #bdbdbd;color:#111;font-size:12px;font-weight:700;text-align:left;text-transform:uppercase;padding:8px 10px;">' . $title . '</th></tr>';
}

function email_row(string $label, string $value): string
{
    return '<tr>'
        . '<td style="width:47%;border:1px solid #c9c9c9;color:#111;font-size:12px;font-weight:700;padding:8px 10px;vertical-align:top;">' . $label . ':</td>'
        . '<td style="border:1px solid #c9c9c9;color:#111;font-size:12px;padding:8px 10px;vertical-align:top;">' . $value . '</td>'
        . '</tr>';
}

$autoloadPath = dirname(__DIR__) . '/vendor/autoload.php';

if (!is_file($autoloadPath)) {
    respond(false, 'Falta instalar PHPMailer. Ejecuta composer install en la carpeta del proyecto.', 500);
}

require $autoloadPath;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Metodo no permitido.', 405);
}

$json = file_get_contents('php://input');
$data = json_decode($json ?: '', true);

if (!is_array($data) || $data === []) {
    $data = $_POST;
}

$requiredFields = [
    'nombre' => 'Nombre completo',
    'cedula' => 'Cedula',
    'sexo' => 'Sexo',
    'edad' => 'Edad',
    'ciudad' => 'Provincia',
    'estado_civil' => 'Estado civil',
    'vivienda' => 'Tipo de vivienda',
    'direccion' => 'Direccion',
    'casa' => 'Casa No.',
    'sector' => 'Sector',
    'celular' => 'Celular',
    'empresa' => 'Empresa',
    'cargo' => 'Cargo',
    'dirtrabajo' => 'Direccion de trabajo',
    'teltrabajo' => 'Telefono de trabajo',
    'tiempoempresa' => 'Tiempo en la empresa',
    'ingresos' => 'Ingresos mensuales',
    'monto' => 'Monto solicitado',
    'tiempo_prestamo' => 'Tiempo del prestamo',
];

foreach ($requiredFields as $field => $label) {
    if (input_value($data, $field) === '') {
        respond(false, "Falta completar el campo: {$label}.", 422);
    }
}

$message = '<!doctype html><html lang="es"><head><meta charset="UTF-8"></head>';
$message .= '<body style="margin:0;padding:0;background:#ffffff;color:#111;font-family:Arial,Helvetica,sans-serif;">';
$message .= '<div style="width:100%;margin:0;padding:0;">';
$message .= '<h2 style="margin:0 0 14px;color:#111;font-size:18px;font-weight:700;">Solicitud de Crédito - Inversiones 3R</h2>';
$message .= '<p style="margin:0 0 18px;color:#333;font-size:12px;">Formulario generado desde nuestra página web.</p>';
$message .= '<table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;border:1px solid #bdbdbd;font-family:Arial,Helvetica,sans-serif;">';
$message .= email_section('DATOS PERSONALES');
$message .= email_row('Nombre', h($data, 'nombre'));
$message .= email_row('Cédula', h($data, 'cedula'));
$message .= email_row('Apodo', h($data, 'apodo', '-'));
$message .= email_row('Sexo', h($data, 'sexo'));
$message .= email_row('Edad', h($data, 'edad'));
$message .= email_row('Ciudad', h($data, 'ciudad'));
$message .= email_row('Estado civil', h($data, 'estado_civil'));
$message .= email_row('Tipo de vivienda', h($data, 'vivienda'));
$message .= email_row('Dirección', h($data, 'direccion'));
$message .= email_row('Casa No.', h($data, 'casa'));
$message .= email_row('Sector', h($data, 'sector'));
$message .= email_row('Tel. Casa', h($data, 'telcasa', '-'));
$message .= email_row('Celular', h($data, 'celular'));
$message .= email_section('INFORMACION LABORAL');
$message .= email_row('Empresa', h($data, 'empresa'));
$message .= email_row('Cargo', h($data, 'cargo'));
$message .= email_row('Dirección de trabajo', h($data, 'dirtrabajo'));
$message .= email_row('Teléfono trabajo', h($data, 'teltrabajo'));
$message .= email_row('Tiempo en la empresa', h($data, 'tiempoempresa'));
$message .= email_row('Ingresos mensuales', h($data, 'ingresos'));
$message .= email_section('REFERENCIAS PERSONALES');
$message .= email_row('Referencia 1', '<strong>Nombre:</strong> ' . h($data, 'ref1', '-') . '<br><strong>Teléfono:</strong> ' . h($data, 'tel1', '-'));
$message .= email_row('Referencia 2', '<strong>Nombre:</strong> ' . h($data, 'ref2', '-') . '<br><strong>Teléfono:</strong> ' . h($data, 'tel2', '-'));
$message .= email_row('Referencia 3', '<strong>Nombre:</strong> ' . h($data, 'ref3', '-') . '<br><strong>Teléfono:</strong> ' . h($data, 'tel3', '-'));
$message .= email_section('DATOS DEL PRESTAMO');
$message .= email_row('Monto solicitado', h($data, 'monto'));
$message .= email_row('Tiempo (meses)', h($data, 'tiempo_prestamo'));
$message .= email_row('Observaciones', h($data, 'observaciones', '-'));
$message .= email_row('Fecha de envío', htmlspecialchars(date('Y-m-d H:i:s'), ENT_QUOTES, 'UTF-8'));
$message .= '</table>';
$message .= '</div></body></html>';

$plainMessage = "NUEVA SOLICITUD DE CREDITO\n";
$plainMessage .= "Nombre: " . input_value($data, 'nombre') . "\n";
$plainMessage .= "Cedula: " . input_value($data, 'cedula') . "\n";
$plainMessage .= "Celular: " . input_value($data, 'celular') . "\n";
$plainMessage .= "Monto solicitado: " . input_value($data, 'monto') . "\n";

$smtpHost = mail_config('SMTP_HOST', 'smtp.gmail.com');
$smtpPort = (int)mail_config('SMTP_PORT', '587');
$smtpUser = mail_config('SMTP_USER');
$smtpPassword = preg_replace('/\s+/', '', mail_config('SMTP_PASSWORD'));
$mailFrom = mail_config('MAIL_FROM', $smtpUser);
$mailFromName = mail_config('MAIL_FROM_NAME', 'Formulario Inversiones 3R');
$mailTo = mail_config('MAIL_TO');

if ($smtpUser === '' || $smtpPassword === '' || $mailTo === '' || $mailFrom === '') {
    respond(false, 'El servidor no tiene configuradas las credenciales de correo.', 500);
}

$mail = new PHPMailer(true);

try {
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    $mail->Host = $smtpHost;
    $mail->SMTPAuth = true;
    $mail->Username = $smtpUser;
    $mail->Password = $smtpPassword;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $smtpPort;

    $mail->setFrom($mailFrom, $mailFromName);
    $mail->addAddress($mailTo);
    $mail->addReplyTo($mailFrom, $mailFromName);

    $mail->isHTML(true);
    $mail->Subject = 'Nueva Solicitud - Inversiones 3R';
    $mail->Body = $message;
    $mail->AltBody = $plainMessage;

    $mail->send();
    respond(true, 'Tu solicitud fue enviada correctamente. Nos pondremos en contacto contigo.');
} catch (Exception $e) {
    error_log('Error enviando solicitud Inversiones 3R: ' . $mail->ErrorInfo);
    respond(false, 'Ocurrio un error al enviar la solicitud. Intentalo nuevamente o comunicate con nosotros.', 500);
}
