<?php
// Allow POST requests from your app
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Get raw POST data
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

// Configure your email
$to = "jaypebayonon@gmail.com";  // 👈 replace with your real email address
$subject = "✈️ New Flight Booking from " . $data['fullName'];

// Email content
$message = "
A new flight booking has been submitted!

Passenger: {$data['fullName']}
Email: {$data['email']}
Flight: {$data['airline']}
Route: {$data['depart']} → {$data['arrive']}
Duration: {$data['duration']}
Price: {$data['price']}
Booking Date: " . date('Y-m-d H:i:s') . "
";

// Additional headers
$headers = "From: noreply@yourdomain.com\r\n";
$headers .= "Reply-To: {$data['email']}\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Send email
if (mail($to, $subject, $message, $headers)) {
    echo json_encode(["success" => true, "message" => "Email sent successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to send email"]);
}
?>

