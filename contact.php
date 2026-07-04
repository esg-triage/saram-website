<?php
// Allow the GitHub Pages site (and eventually the custom domain) to POST here.
// Once saramdigitech.com points to GitHub Pages, update this to the real domain.
$allowed_origins = [
    'https://esg-triage.github.io',
    'https://saramdigitech.com',
    'https://www.saramdigitech.com',
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
}
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Method not allowed']));
}

$name     = strip_tags(trim($_POST['name']     ?? ''));
$email    = filter_var(trim($_POST['email']    ?? ''), FILTER_SANITIZE_EMAIL);
$company  = strip_tags(trim($_POST['company']  ?? ''));
$practice = strip_tags(trim($_POST['practice'] ?? ''));
$message  = strip_tags(trim($_POST['message']  ?? ''));

if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$message) {
    http_response_code(400);
    exit(json_encode(['error' => 'Name, valid email and message are required']));
}

$to      = 'hello@saramdigitech.com'; // ← change to your inbox
$subject = "Website enquiry: $practice — $name";
$body    = implode("\n", [
    "Name:     $name",
    "Company:  $company",
    "Email:    $email",
    "Practice: $practice",
    "",
    "Message:",
    $message,
]);
$headers = implode("\r\n", [
    "From: website@saramdigitech.com",
    "Reply-To: $email",
    "X-Mailer: PHP/" . PHP_VERSION,
]);

if (mail($to, $subject, $body, $headers)) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Mail send failed']);
}
