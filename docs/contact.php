<?php
/**
 * contact.php — Hostinger server-side contact form handler
 *
 * This file is NOT part of the GitHub Pages static site.
 * Upload it to: Hostinger → File Manager → public_html/contact.php
 *
 * Receives POST requests from the contact form on saramdigitech.com/contact/
 * and sends the enquiry to info@saramdigitech.com via PHP mail().
 */

// Allow POST from the GitHub Pages site and the custom domain.
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

$to = 'info@saramdigitech.com';

// MIME-encode the subject so special characters (em dash etc.) render correctly.
$subject = mb_encode_mimeheader(
    "Website enquiry: {$practice} \xe2\x80\x94 {$name}",
    'UTF-8',
    'B'
);

$body = implode("\n", [
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
    "Content-Type: text/plain; charset=UTF-8",
    "X-Mailer: PHP/" . PHP_VERSION,
]);

if (mail($to, $subject, $body, $headers)) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Mail send failed']);
}
