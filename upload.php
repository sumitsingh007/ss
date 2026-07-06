<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recipient email
    $to = "amraydigital@gmail.com";
    $subject = "AMRECO Survey Response";

    // Compose email body with form fields
    $body = "AMRECO Survey Submission:\n\n";
    foreach ($_POST as $key => $value) {
        // For array values such as multi-selects
        if (is_array($value)) {
            $body .= ucfirst(str_replace("_", " ", $key)) . ": " . implode(", ", $value) . "\n";
        } else {
            $body .= ucfirst(str_replace("_", " ", $key)) . ": " . $value . "\n";
        }
    }

    // Email headers
    $boundary = md5(time());
    $headers = "From: noreply@amray-group.com\r\n";
    $headers .= "Reply-To: noreply@amray-group.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"\r\n";

    // Multipart body
    $message = "--$boundary\r\n";
    $message .= "Content-Type: text/plain; charset=utf-8\r\n";
    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $message .= $body . "\r\n";

    // Handle file uploads
    if (!empty($_FILES)) {
        foreach ($_FILES as $file) {
            if (isset($file['tmp_name']) && is_uploaded_file($file['tmp_name'])) {
                $filename = $file['name'];
                $filedata = file_get_contents($file['tmp_name']);
                $filetype = $file['type'];
                $message .= "--$boundary\r\n";
                $message .= "Content-Type: $filetype; name=\"$filename\"\r\n";
                $message .= "Content-Disposition: attachment; filename=\"$filename\"\r\n";
                $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $message .= chunk_split(base64_encode($filedata)) . "\r\n";
            }
        }
    }

    $message .= "--$boundary--";

    // Send the email
    if (mail($to, $subject, $message, $headers)) {
        echo "<div style='background: #d0ffd5; color: #226622; padding: 1em; border-radius: 8px;'>Thank you for your submission. We have received your response.</div>";
    } else {
        echo "<div style='background: #ffd5d5; color: #992222; padding: 1em; border-radius: 8px;'>There was an error sending your response. Please try again.</div>";
    }
}
?>