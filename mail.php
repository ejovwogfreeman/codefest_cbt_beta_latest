<?php

// mail
// exams@codefest.africa
// mail password
//trostdeal12345#

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// function sendEmail($gmail, $subject, $htmlFilePath)

function sendEmail($to, $subject, $htmlFilePath, $emailAddress, $password)
{
    // Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        // Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host       = 'codefestuniversity.online';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'exams@codefest.africa';
        $mail->Password   = 'exams@codefest.africa'; // Update with your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom('exams@codefest.africa', 'Codefest CBT');
        $mail->addAddress($to);
        $mail->addReplyTo('exams@codefest.africa', 'Codefest CBT');

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;

        // Load HTML content from file
        $htmlContent = file_get_contents($htmlFilePath);

        // Replace placeholders with actual values
        $htmlContent = str_replace(['{{email}}', '{{password}}'], [$emailAddress, $password], $htmlContent);

        $mail->Body    = $htmlContent;
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        // echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
