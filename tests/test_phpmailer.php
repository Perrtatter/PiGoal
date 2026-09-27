<?php
// 1. Process form submission FIRST before rendering any HTML structure
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require_once __DIR__ . '/../compenents/get_env/get_env.php';

$message_status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["to_email"])) {
    
    $server   = get_env("../", "smtp_server");
    $port     = get_env("../", "smtp_port");
    $password = get_env("../", "smtp_password");
    $username = get_env("../", "smtp_username");

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug  = SMTP::DEBUG_SERVER;                     
        $mail->isSMTP();                                            
        $mail->Host       = $server;                     
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = $username;                     
        $mail->Password   = $password;                               

        // Handle structural security protocol based on port mapping
        if ((int)$port === 465) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         
        }
        $mail->Port = $port;                                    

        // Local macOS OpenSSL Certificate Fix 
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Recipients
        $mail->setFrom($username, 'Mailer');
        $mail->addAddress($_POST["to_email"], 'Target');     

        // Content
        $mail->isHTML(true);                                  
        $mail->Subject = 'Here is the subject';
        $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        $message_status = "<p style='color: green;'>Message has been sent successfully!</p>";
    } catch (Exception $e) {
        $message_status = "<p style='color: red;'>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test SMTP Connection</title>
</head>
<body>

    <?php 
    // Display submission feedback context right above the input form layout
    if (!empty($message_status)) {
        echo "<div>" . $message_status . "</div>";
    }
    ?>

    <form method="post" action="">
        <label for="to_email">Recipient Email:</label>
        <input type="email" id="to_email" name="to_email" required>
        <button type="submit">Send Email</button>
    </form>

</body>
</html>
