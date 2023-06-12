<?php
require("./mailing/mailfunction.php");




use PHPMailer\PHPMailer\PHPMailer;

use PHPMailer\PHPMailer\Exception;
$mail = new PHPMailer(true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $name = $_POST['name'];
    $position = $_POST['position'];
    $email = $_POST['email'];
    $status = $_POST['status'];
    $experience = $_POST['experience'];
    $details = $_POST['details'];

    // File upload handling
    $targetDirectory = "uploads/";
    $targetFile = $targetDirectory . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if file is selected
    if (!empty($_FILES["fileToUpload"]["tmp_name"])) {
        // Allow only specific file types
        $allowedExtensions = array("doc", "docx", "pdf");
        if (!in_array($fileType, $allowedExtensions)) {
            echo "Invalid file format. Only DOC, DOCX, and PDF files are allowed.";
            exit();
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
            echo "File uploaded successfully.";
        } else {
          
            exit();
        }
    }

    // Connect to the database
    $servername = "localhost";
    $username = "root";
    $password = ""; // Set your MySQL password if you have one
    $dbname = "careers_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert the form data into the database
    $sql = "INSERT INTO applications (name, position, email, status, experience, details, resume_path) 
            VALUES ('$name', '$position', '$email', '$status', '$experience', '$details', '$targetFile')";

    if ($conn->query($sql) === TRUE) {
        try {
            // SMTP configuration
            $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'admin@softbuilders.com';
                $mail->Password = 'tigemwnmgfekabjl';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

            // Set sender, recipient, subject, and message
            $mail->setFrom($_POST['email'], 'Applicant');
            $mail->addAddress('admin@example.com');
            $mail->Subject = 'New Job Application';
            $mail->Body = "Name: $name\nPosition: $position\nEmail: $email\nStatus: $status\nExperience: $experience\nDetails: $details";

            // Attach the uploaded resume
            $mail->addAttachment($targetFile, basename($targetFile));

            // Send the email
            if ($mail->send()) {
                // Redirect to success page
                echo '<script>alert("Application submitted successfully."); window.location.href = "careers.html?message=success";</script>';
                exit();
            } else {
                // Redirect to error page
                echo '<script>alert("Failed to send email. Please try again later."); window.location.href = "careers.html?message=error";</script>';
                exit();
            }
        } catch (Exception $e) {
            // Redirect to error page
            echo '<script>alert("Failed to send email. Please try again later."); window.location.href = "careers.html?message=error";</script>';
            exit();
        }
    } else {
        // Redirect to error page
        echo '<script>alert("Failed to submit application. Please try again later."); window.location.href = "careers.html?message=error";</script>';
        exit();
    }
}
?>
