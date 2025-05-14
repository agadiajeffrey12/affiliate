<?php
// Set headers to handle AJAX requests
header('Content-Type: application/json');

// Check if the form was submitted
if (isset($_POST['formSubmit'])) {
    // Get form data
    $fullName = filter_input(INPUT_POST, 'fullName', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $accountType = filter_input(INPUT_POST, 'accountType', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    
    // Validate required fields
    if (empty($fullName) || empty($email) || empty($phone) || empty($accountType)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
        exit;
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
        exit;
    }
    
    // Current timestamp for the submission
    $date = date('Y-m-d H:i:s');
    
    // Prepare email to site owner
    $to = 'agadiajeffrey@gmail.com'; // Replace with site owner's email
    $subject = 'New Trading Account Request from ' . $fullName;
    
    $emailBody = "
    <html>
    <head>
        <title>New Trading Account Request</title>
    </head>
    <body>
        <h2>New Trading Account Request</h2>
        <p><strong>Date:</strong> {$date}</p>
        <p><strong>Name:</strong> {$fullName}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Phone:</strong> {$phone}</p>
        <p><strong>Account Type:</strong> {$accountType}</p>
        <p><strong>Message:</strong> {$message}</p>
    </body>
    </html>
    ";
    
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: {$email}" . "\r\n";
    
    // Save to database (optional - uncomment and modify if you have a database)
    /*
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare('INSERT INTO leads (full_name, email, phone, account_type, message, date_submitted) 
                              VALUES (:full_name, :email, :phone, :account_type, :message, :date_submitted)');
        
        $stmt->execute([
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'account_type' => $accountType,
            'message' => $message,
            'date_submitted' => $date
        ]);
    } catch (PDOException $e) {
        // Log error but don't expose to user
        error_log('Database Error: ' . $e->getMessage());
    }
    */
    
    // Attempt to send email
    $mailSent = mail($to, $subject, $emailBody, $headers);
    
    // Create a backup file with the submission (in case email fails)
    $backupData = "Date: {$date}\nName: {$fullName}\nEmail: {$email}\nPhone: {$phone}\n";
    $backupData .= "Account Type: {$accountType}\nMessage: {$message}\n\n";
    
    $backupFile = 'leads/leads_backup.txt';
    file_put_contents($backupFile, $backupData, FILE_APPEND);
    
    // Return success response
    echo json_encode(['success' => true, 'message' => 'Your request has been received']);
    
} else {
    // If someone tries to access this file directly
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>