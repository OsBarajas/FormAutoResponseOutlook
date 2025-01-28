<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

function createLists($data){
    $output = "<ol style='padding:0px; margin:0px;'>";
    foreach($data as $d){
        $output .= "<li>$d</li>";
    }
    $output .= "</ol>";
    return $output;
}

// Collect input data
$data = [
    'incidentNumber' => $_POST['incidentNumber'] ?? '',
    'datetime' => $_POST['datetime'] ?? '',
    'priority' => $_POST['priority'] ?? '',
    'location' => $_POST['location'] ?? '',
    'severity' => $_POST['severity'] ?? '',
    'summary' => $_POST['summary'] ?? '',
    'description' => $_POST['description'] ?? '',
    'impact' => $_POST['impact'] ?? '',
    'expectedResults' => $_POST['expectedResults'] ?? '',
    'actualResults' => $_POST['actualResults'] ?? '',
    'workaround' => $_POST['workaround'] ?? '',
    'membersList' => createLists($_POST['membersList'] ?? []),
    'incidentsList' => createLists($_POST['incidentsList'] ?? []),
    'channel' => $_POST['channel'] ?? '',
    'state' => $_POST['state'] ?? '',
    'lob' => $_POST['lob'] ?? '',
    'steps' => createLists($_POST['steps'] ?? []),
    'observations' => $_POST['observations'] ?? '',
];

// Base URL for file uploads
$baseUrl = 'https://salmandeveloper.net/client-project/formtemplate/uploads/';

// Validate recipient emails
$recipients = isset($_POST['recipients']) ? explode(',', $_POST['recipients']) : [];
if (empty($recipients)) {
    echo json_encode(['success' => false, 'message' => 'No recipients provided.']);
    exit;
}


// Process file uploads
$uploadedFiles = [];
if (!empty($_FILES['files']['name'][0])) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    foreach ($_FILES['files']['tmp_name'] as $index => $tmpName) {
        $originalName = basename($_FILES['files']['name'][$index]);
        $fileName = $originalName;
        $filePath = $uploadDir . $fileName;

        // Check if file already exists and rename if necessary
        $fileCounter = 1;
        $fileInfo = pathinfo($fileName);
        while (file_exists($filePath)) {
            $fileName = $fileInfo['filename'] . '_' . $fileCounter . '.' . $fileInfo['extension'];
            $filePath = $uploadDir . $fileName;
            $fileCounter++;
        }

        // Move the uploaded file to the server
        if (move_uploaded_file($tmpName, $filePath)) {
            $uploadedFiles[] = $baseUrl . $fileName;
        }
    }
}



// Generate file links for the email
$fileLinks = '';
foreach ($uploadedFiles as $fileUrl) {
    $fileName = basename($fileUrl);
    $filePath = $uploadDir . $fileName;

    // Get MIME type to check if it's an image
    $fileType = mime_content_type($filePath);
    if (strpos($fileType, 'image') === 0) {
        // If the file is an image, display it directly
        $fileLinks .= "<img src='$fileUrl' alt='$fileName' style='max-width: 100%; height: auto;'><br>";
    } else {
        // Otherwise, provide a download link
        $fileLinks .= "<a href='$fileUrl' target='_blank'>$fileName</a><br>";
    }
}

$data['files'] = $fileLinks;

// Read and replace macros in the HTML template
$templateFile = 'email.html';
if (!file_exists($templateFile)) {
    echo json_encode(['success' => false, 'message' => 'Email template not found.']);
    exit;
}

$templateContent = file_get_contents($templateFile);
foreach ($data as $key => $value) {
    $templateContent = str_replace("%$key%", nl2br(ucfirst($value)), $templateContent);
}

// Construct the email
$subject = "Incident Report - {$data['incidentNumber']} - {$data['summary']}";
$headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
];

// Send the email
$success = true;
foreach ($recipients as $recipient) {
    if (!mail($recipient, $subject, $templateContent, implode("\r\n", $headers))) {
        $success = false;
        break;
    }
}

// Return response
if ($success) {
    echo json_encode(['success' => true, 'message' => 'Email sent successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send email.']);
}