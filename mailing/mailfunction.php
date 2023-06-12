<?php

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendEmail($to, $subject, $body, $attachmentPath = null)
{
    try {
        $mail = new PHPMailer(true);

        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'samiuddinbirgoshi@gmail.com'; // Replace with your email address
        $mail->Password = 'your-password'; // Replace with your email password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Set sender, recipient, subject, and message
        $mail->setFrom('your-email@gmail.com', 'Your Name'); // Replace with your email address and name
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $body;

        // Attach the file if provided
        if ($attachmentPath !== null) {
            $mail->addAttachment($attachmentPath);
        }

        // Send the email
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Handle exception or log error
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data
    $name = $_POST["name"];
    $position = $_POST["position"];
    $email = $_POST["email"];
    $experience = $_POST["experience"];
    $otherdetails = $_POST["details"];
    $filename = $_FILES["fileToUpload"]["name"];
    $filetype = $_FILES["fileToUpload"]["type"];
    $filesize = $_FILES["fileToUpload"]["size"];
    $tempfile = $_FILES["fileToUpload"]["tmp_name"];
    $filenameWithDirectory = "./uploads/" . $name . "." . pathinfo($filename, PATHINFO_EXTENSION); // Change the path to the desired directory

    // Validate uploaded file
    $allowedExtensions = array("pdf", "png");
    $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedExtensions)) {
        echo '<center><h1>Invalid file format! Only PDF and PNG files are allowed.</h1></center>';
        exit;
    }

    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = ""; // Replace with your database password
    $dbname = "career_form_db"; // Replace with your database name

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the database query
    $stmt = $conn->prepare("INSERT INTO career (name, position, email, experience, otherdetails, filename) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ssssss", $name, $position, $email, $experience, $otherdetails, $filenameWithDirectory);
    if ($stmt->execute() === false) {
        die("Error executing statement: " . $stmt->error);
    }

    // Check if the query was successful
    if ($stmt->affected_rows > 0) {
        // Move the uploaded file to the desired directory
        if (move_uploaded_file($tempfile, $filenameWithDirectory)) {
            // Send email
            $body = "<ul><li>Name: " . $name . "</li><li>Phone: " . $position . "</li><li>Email: " . $email . "</li></li><li>Experience: " . $experience . " Yrs.</li><li>Resume (Attached Below):</li></ul>";
            $status = sendEmail("softbuildersdubai@gmail.com", "sam hunzai", $body, $filenameWithDirectory); // Receiver's email address

            if ($status) {
                echo '<script>alert("Application submitted successfully.");</script>';
                // Redirect to success page
                echo '<script>window.location.href = "careers.html?message=success";</script>';
            } else {
                echo '<script>alert("Application submitted successfully.");</script>';
                // Redirect to success page");</script>';
                // Redirect to error page
                echo '<script>window.location.href = "careers.html?message=error";</script>';
            }
        } else {
            echo '<script>alert("Error uploading file. Please try again.");</script>';
            // Redirect to error page
            echo '<script>window.location.href = "careers.html?message=error";</script>';
        }
    } else {
        echo '<script>alert("Error submitting application. Please try again.");</script>';
        // Redirect to error page
        echo '<script>window.location.href = "careers.html?message=error";</script>';
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
} else {
    echo '<script>alert("Invalid request.");</script>';
    // Redirect to error page
    echo '<script>window.location.href = "careers.html?message=error";</script>';
}
?>
