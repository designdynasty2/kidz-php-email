<?php
// sendmail.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

// Simple server-side validation & sanitization
function get_post($key) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}
$name    = htmlspecialchars(strip_tags(get_post('name')));
$email   = filter_var(get_post('email'), FILTER_VALIDATE_EMAIL) ? get_post('email') : '';
$phone   = htmlspecialchars(strip_tags(get_post('phonenumber')));
$message = htmlspecialchars(strip_tags(get_post('message')));

if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Please fill required fields (name, email, message).']);
    exit;
}

// SMTP credentials (replace with your actual SMTP host + credentials)
$smtpHost     = 'smtp.gmail.com';           // e.g., smtp.hostinger.com or smtp.gmail.com
$smtpUsername = 'designdynasty84@gmail.com';
$smtpPassword = 'fmcs licg gskt yonm';
$smtpPort     = 587;                           // or 465 for SSL
$smtpSecure   = PHPMailer::ENCRYPTION_STARTTLS; // or PHPMailer::ENCRYPTION_SMTPS for 465

$recipientEmail = 'savitha848410@gmail.com';
$recipientName  = 'Kidz Montessori Academy';

// Build the message HTML
$bodyHtml = "
    <h3>Contact Form Submission</h3>
    <p><strong>Name:</strong> {$name}</p>
    <p><strong>Email:</strong> {$email}</p>
    <p><strong>Phone:</strong> {$phone}</p>
    <p><strong>Message:</strong><br/>" . nl2br($message) . "</p>
";

$mail = new PHPMailer(true);

// Common SMTP setup function to reuse
function setupSMTP(PHPMailer $m, $host, $user, $pass, $port, $secure) {
    $m->isSMTP();
    $m->Host       = $host;
    $m->SMTPAuth   = true;
    $m->Username   = $user;
    $m->Password   = $pass;
    $m->SMTPSecure = $secure;
    $m->Port       = $port;
    // Optional: increase timeouts if server is slow
    $m->Timeout    = 30;
    $m->SMTPAutoTLS = true;
}

// Attempt 1: Try spoofing (From = visitor). If it fails, we catch and retry safe mode.
try {
    setupSMTP($mail, $smtpHost, $smtpUsername, $smtpPassword, $smtpPort, $smtpSecure);

    // SPOOF ATTEMPT: set From = visitor
    $mail->setFrom($email, $name);               // risky, may be rejected or marked spam
    $mail->addAddress($recipientEmail, $recipientName);

    // It's still useful to add Reply-To (redundant in spoof case but harmless)
    $mail->addReplyTo($email, $name);

    $mail->isHTML(true);
    $mail->Subject = "Contact Form: {$name}";
    $mail->Body    = $bodyHtml;
    $mail->AltBody = strip_tags("Name: {$name}\nEmail: {$email}\nPhone: {$phone}\n\nMessage:\n{$message}");

    $mail->send();

    // If send() didn't throw, spoof succeeded
    echo json_encode(['status' => 'success', 'mode' => 'spoofed', 'message' => 'Email sent (From set to visitor).']);
    exit;

} catch (Exception $e) {
    // Spoof failed. We'll attempt the safe default now.
    // (Log the error for debugging - in production send to a file or monitoring system.)
    $errorInfo = $mail->ErrorInfo ?? $e->getMessage();
    // Clear recipients and attachments before retrying
    $mail->clearAddresses();
    $mail->clearAllRecipients();
    $mail->clearAttachments();

    try {
        // Setup SMTP again (fresh state)
        setupSMTP($mail, $smtpHost, $smtpUsername, $smtpPassword, $smtpPort, $smtpSecure);

        // SAFE DEFAULT: From = your domain account; Reply-To = visitor
        $mail->setFrom($smtpUsername, 'Kidz Montessori Academy'); // verified sender
        $mail->addAddress($recipientEmail, $recipientName);
        $mail->addReplyTo($email, $name);

        $mail->isHTML(true);
        $mail->Subject = "Contact Form: {$name}";
        $mail->Body    = $bodyHtml;
        $mail->AltBody = strip_tags("Name: {$name}\nEmail: {$email}\nPhone: {$phone}\n\nMessage:\n{$message}");

        $mail->send();

        echo json_encode([
            'status' => 'success',
            'mode' => 'safe_default',
            'message' => 'Email sent using safe default (From = your domain, Reply-To = visitor).',
            'spoof_error' => $errorInfo
        ]);
        exit;

    } catch (Exception $e2) {
        // Both attempts failed. Return detailed message (but avoid leaking sensitive info in production).
        $err2 = $mail->ErrorInfo ?? $e2->getMessage();
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Both spoof and safe send attempts failed.',
            'spoof_error' => $errorInfo,
            'safe_error' => $err2
        ]);
        exit;
    }
}