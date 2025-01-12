<?php
// Database connection
$servername = "localhost";
$username = "tvetikmb";
$password = "T87d4+E]fe1gMF";
$dbname = "tvetikmb_customer_reports";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $full_name = $_POST['full-name'];
    $email = $_POST['email'];
    $report_title = $_POST['report-title'];
    $report_date = $_POST['date'];
    $model_name = $_POST['model-name'];
    $serial_number = $_POST['serial-number'];
    $report_description = $_POST['report-description'];

    // SQL query to insert data
    $sql = "INSERT INTO customer_reports (full_name, email, report_title, report_date, model_name, serial_number, report_description) 
            VALUES ('$full_name', '$email', '$report_title', '$report_date', '$model_name', '$serial_number', '$report_description')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
