<?php
// Include PHPMailer autoloader
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Validate form inputs (example validation, modify as per your requirements)
    if (empty($name) || empty($email) || empty($phone) || empty($subject) || empty($message)) {
        // Handle validation error (e.g., display an error message)
        
        echo '<script>alert("pleasee fill out all the sections");</script>';
        // Redirect to home page with error message
     echo '<script>window.location.href = "contact.html?message=error";</script>';
    } else {
        // Connect to the database
        $servername = "localhost";
        $username = "root";
        $password = ""; // Set your MySQL password if you have one
        $dbname = "contact_form_db";

        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check the connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Insert the form data into the database
        $sql = "INSERT INTO contacts (name, email, phone, subject, message) VALUES ('$name', '$email', '$phone', '$subject', '$message')";

        if ($conn->query($sql) === TRUE) {
            // Form data stored successfully, send email
            $mail = new PHPMailer(true);
            try {
                // SMTP configuration
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'admin@softbuilders.com';
                $mail->Password = 'tigcmwrmgfekazjl';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;
            
                // Set sender, recipient, subject, and message
                $mail->setFrom($_POST['email'], 'SOFTBUILDERS | DUBAI');
                $mail->addAddress('admin@softbuilders.com');
                $mail->Subject = 'New Email From SoftBuilders - Coming Soon';
                $mail->Body = "The User '{$_POST['email']}' just filled and submitted the form in your coming soon page at softbuilders.com.";
            
                // Send the email
                $mail->send();
                echo '<script>alert("thanks for Contacting with us we will be right back");</script>';
                echo '<script>window.location.href = "contact.php";</script>';
                // Redirect to home page with success message
                echo '<script>alert("thanks for contacting us we will right back."); window.location.href = "contact.html?message=success";</script>';
            
                exit();
            
            } catch (Exception $e) {
                // Output the error message
                echo 'Failed to send email: ' . $mail->ErrorInfo;
                echo '<script>alert("Error Send a Email");</script>';
                // Redirect to home page with error message
             echo '<script>window.location.href = "contact.html?message=error";</script>';
                exit();
            }
        }
    }
}
?>
