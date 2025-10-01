<?php
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

function get_post($key) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

$name    = htmlspecialchars(strip_tags(get_post('name')));
$email   = filter_var(get_post('email'), FILTER_VALIDATE_EMAIL) ? get_post('email') : '';
$phone   = htmlspecialchars(strip_tags(get_post('phonenumber')));
$message = htmlspecialchars(strip_tags(get_post('message')));

if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Please fill required fields.']);
    exit;
}

$smtpHost     = 'smtp.gmail.com';
$smtpUsername = 'designdynasty84@gmail.com';
$smtpPassword = 'fmcs licg gskt yonm';
$smtpPort     = 587;
$smtpSecure   = PHPMailer::ENCRYPTION_STARTTLS;

$recipientEmail = 'savitha848410@gmail.com';
$recipientName  = 'Kidz Montessori Academy';

$bodyHtml = "
<h3>Contact Form Submission</h3>
<p><strong>Name:</strong> {$name}</p>
<p><strong>Email:</strong> {$email}</p>
<p><strong>Phone:</strong> {$phone}</p>
<p><strong>Message:</strong><br/>" . nl2br($message) . "</p>
";

$mail = new PHPMailer(true);
$mail->SMTPDebug = 2;          // Show SMTP debug in browser
$mail->Debugoutput = 'html';

try {
    $mail->isSMTP();
    $mail->Host       = $smtpHost;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUsername;
    $mail->Password   = $smtpPassword;
    $mail->SMTPSecure = $smtpSecure;
    $mail->Port       = $smtpPort;

    // Safe mode: From = your domain, Reply-To = visitor
    $mail->setFrom($smtpUsername, 'Kidz Montessori Academy');
    $mail->addAddress($recipientEmail, $recipientName);
    $mail->addReplyTo($email, $name);

    $mail->isHTML(true);
    $mail->Subject = "Contact Form: {$name}";
    $mail->Body    = $bodyHtml;
    $mail->AltBody = strip_tags("Name: {$name}\nEmail: {$email}\nPhone: {$phone}\nMessage:\n{$message}");

    $mail->send();

    echo json_encode(['status' => 'success', 'message' => 'Your message has been sent successfully!']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Mailer Error: ' . $mail->ErrorInfo
    ]);
}