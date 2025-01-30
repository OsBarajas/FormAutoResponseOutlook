<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Verificar si el método de solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Recopilar datos del formulario
$data = [
    'namerepr' => $_POST['namerepr'] ?? '',
    'recipients' => $_POST['recipients'] ?? '',
];

// Validar correos electrónicos de los destinatarios
$recipients = isset($data['recipients']) ? explode(',', $data['recipients']) : [];
if (empty($recipients)) {
    echo json_encode(['success' => false, 'message' => 'No recipients provided.']);
    exit;
}

// Leer y reemplazar macros en la plantilla de correo electrónico
$templateContent = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incident without sufficient information</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .navbar {
            background-color: #1E3A8A;
            padding: 15px;
            display: flex;
            align-items: center;
            color: white;
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
        }
        .navbar img {
            height: 40px;
            margin-right: 20px;
        }
        .navbar h1 {
            font-size: 24px;
            margin: 0;
        }
        .content {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #ffffff;
        }
        .content p {
            margin-bottom: 15px;
            font-size: 16px;
        }
        .content .info {
            font-weight: bold;
            color: #1E3A8A;
        }
        .footer {
            background-color: #1E3A8A;
            padding: 10px;
            text-align: center;
            color: white;
            font-size: 14px;
            margin-top: 20px;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <img src="logo.png" alt="Logo">
        <h1>Incident without sufficient information</h1>
    </div>

    <div class="content">
        <p>Dear %namerepr%,</p>
        <p>We appreciate the information you have sent to us, however, there are missing some important information that allows us to troubleshoot.</p>
        <p><span class="info">Missing information:</span> %missingInfo%</p>
        <p><span class="info">Additional notes:</span> %additionalNotes%</p>
        <p>%files%</p>
        <p>Best regards,<br>Your Company</p>
    </div>

    <div class="footer">
        Availability Command Center (ACC)
    </div>

</body>
</html>

';

foreach ($data as $key => $value) {
    $templateContent = str_replace("%$key%", nl2br(ucfirst($value)), $templateContent);
}

// Construir el correo electrónico
$subject = "Missing Information Report";
$headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
];

// Enviar el correo electrónico
$success = true;
foreach ($recipients as $recipient) {
    if (!mail(trim($recipient), $subject, $templateContent, implode("\r\n", $headers))) {
        $success = false;
        break;
    }
}

// Devolver la respuesta
if ($success) {
    echo json_encode(['success' => true, 'message' => 'Email sent successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send email.']);
}
?>
