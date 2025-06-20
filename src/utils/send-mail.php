<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function SendMail($email, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();

        $mail->Host = $_ENV["SMTP_HOST"];
        $mail->Port = $_ENV["SMTP_PORT"];

        $mail->SMTPAuth = true;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->Username = $_ENV["SMTP_USER"];
        $mail->Password = $_ENV["SMTP_PASSWORD"];

        $mail->setFrom($_ENV["SMTP_USER"], $_ENV["SMTP_NAME"]);
        $mail->addAddress($email);
        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body = $body;

        $mailSent = $mail->send();
        return $mailSent;
    } catch (Exception $e) {
        if (DEV) {
            echo $mail->ErrorInfo;
        }

        return false;
    }
}