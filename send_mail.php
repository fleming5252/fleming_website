<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

header('Content-Type: application/json');

// ── Get form data ──
$name    = htmlspecialchars(trim($_POST['name'] ?? ''));
$email   = htmlspecialchars(trim($_POST['email'] ?? ''));
$phone   = htmlspecialchars(trim($_POST['phone'] ?? ''));
$message = htmlspecialchars(trim($_POST['message'] ?? ''));

// Basic validation
if (!$name || !$email || !$message) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

// ── PHPMailer Setup ──
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'fleming.embedded20@gmail.com';   // ← your Gmail
    $mail->Password   = ''; // ← Gmail App Password (not your login password)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // ── From / To ──
    $mail->setFrom('fleming.embedded20@gmail.com', 'Fleming Website');
    $mail->addAddress('fleming.embedded20@gmail.com', 'Fleming Enquiries'); // sends to yourself
    $mail->addReplyTo($email, $name); // reply goes to the user

    // ── Email Content ──
    $mail->isHTML(true);
    $mail->Subject = 'Enquiry from ' . $name;
    $mail->Body    = '
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="UTF-8"/>
      <style>
        body { margin:0; padding:0; background:#f5f7ff; font-family: Arial, sans-serif; }
        .wrap { max-width:600px; margin:40px auto; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08); }
        .header { background:#1a56db; padding:36px 40px; text-align:center; }
        .header h1 { color:#ffffff; margin:0; font-size:22px; letter-spacing:1px; }
        .header p { color:rgba(255,255,255,0.75); margin:8px 0 0; font-size:13px; }
        .body { padding:36px 40px; }
        .label { font-size:11px; font-weight:700; color:#1a56db; text-transform:uppercase; letter-spacing:1px; margin-bottom:4px; }
        .value { font-size:15px; color:#111111; margin-bottom:24px; padding:12px 16px; background:#f9fafb; border-radius:8px; border-left:3px solid #1a56db; }
        .message-value { font-size:14px; color:#374151; line-height:1.7; white-space:pre-wrap; }
        .footer { background:#f9fafb; padding:20px 40px; text-align:center; border-top:1px solid #e5e7eb; }
        .footer p { font-size:12px; color:#9ca3af; margin:0; }
        .footer span { color:#1a56db; font-weight:600; }
      </style>
    </head>
    <body>
      <div class="wrap">
        <div class="header">
          <h1>NEW ENQUIRY</h1>
          <p>You have received a new message from your website contact form</p>
        </div>
        <div class="body">
          <p class="label">Full Name</p>
          <p class="value">' . $name . '</p>

          <p class="label">Email Address</p>
          <p class="value">' . $email . '</p>

          <p class="label">Phone Number</p>
          <p class="value">' . ($phone ?: 'Not provided') . '</p>

          <p class="label">Message</p>
          <p class="value message-value">' . nl2br($message) . '</p>
        </div>
        <div class="footer">
          <p>This email was sent from the contact form at <span>fleming.co.in</span></p>
        </div>
      </div>
    </body>
    </html>';

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Message sent successfully!']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to send. Error: ' . $mail->ErrorInfo]);
}
?>