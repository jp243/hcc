<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Path if manually uploaded (adjust if needed)
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// Get JSON POST data
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Validate fields
if (
    empty($data['fullName']) ||
    empty($data['email']) ||
    empty($data['airline']) ||
    empty($data['depart']) ||
    empty($data['arrive'])
) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

// Your SMTP credentials
$mail = new PHPMailer(true);
try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';       // or your mail server (e.g. mail.yourdomain.com)
    $mail->SMTPAuth   = true;
    $mail->Username   = 'your_email@gmail.com'; // your SMTP username
    $mail->Password   = 'your_app_password';    // Gmail App Password or SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // 'tls' encryption
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('your_email@gmail.com', 'Flight Booking App');
    $mail->addAddress('your_email@gmail.com', 'Admin'); // where bookings are sent
    $mail->addReplyTo($data['email'], $data['fullName']);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = "✈️ New Flight Booking from " . htmlspecialchars($data['fullName']);

    $mail->Body = "
        <h2>New Flight Booking Received</h2>
        <p><strong>Passenger:</strong> {$data['fullName']}</p>
        <p><strong>Email:</strong> {$data['email']}</p>
        <p><strong>Flight:</strong> {$data['airline']}</p>
        <p><strong>Route:</strong> {$data['depart']} → {$data['arrive']}</p>
        <p><strong>Duration:</strong> {$data['duration']}</p>
        <p><strong>Price:</strong> \${$data['price']}</p>
        <hr>
        <p><em>Booking submitted on " . date('Y-m-d H:i:s') . "</em></p>
    ";

    $mail->AltBody = "New flight booking from {$data['fullName']} - {$data['airline']} ({$data['depart']} to {$data['arrive']})";

    $mail->send();

    echo json_encode(["success" => true, "message" => "Email sent successfully"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Mailer Error: {$mail->ErrorInfo}"]);
}
?>
