<?php
// Set the recipient email address
$to_email = "trishuldeepcleaningservices@gmail.com";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and validate input data
    $name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST['phone']) ? strip_tags(trim($_POST['phone'])) : '';
    $subject = isset($_POST['subject']) ? strip_tags(trim($_POST['subject'])) : 'New Quote Request'; // Default subject if missing
    $service = isset($_POST['service']) ? strip_tags(trim($_POST['service'])) : ''; // New field for service
    $message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : '';
    
    // Validate required fields (Subject is optional now as it has a default, Service is optional)
    if (empty($name) || empty($email) || empty($phone) || empty($message)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }
    
    // Validate email address
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit;
    }
    
    // Validate phone number (10 digits)
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please enter a valid 10-digit phone number.']);
        exit;
    }
    
    // Create email subject
    $email_subject = "Contact Form: " . $subject;
    if (!empty($service)) {
        $email_subject .= " - Service: " . $service;
    }
    
    // Create email body
    $email_body = "You have received a new message from the contact form on your website.\n\n";
    $email_body .= "Here are the details:\n\n";
    $email_body .= "Name: $name\n";
    $email_body .= "Email: $email\n";
    $email_body .= "Phone: $phone\n";
    if (!empty($service)) {
        $email_body .= "Service Interested: $service\n";
    }
    $email_body .= "Subject: $subject\n\n";
    $email_body .= "Message:\n$message\n";
    
    // Set email headers
    // Use a domain-based email for From to avoid spam filters (DMARC/SPF policies)
    // Assuming the domain is trishuldeepcleaningservices.com based on the canonical URL in HTML
    $from_email = "no-reply@trishuldeepcleaningservices.com"; 
    
    $headers = "From: $from_email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Send the email
    // The "-f" parameter sets the envelope sender, which is crucial for deliverability on many servers.
    if (mail($to_email, $email_subject, $email_body, $headers, "-f$from_email")) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Thank you! Your message has been sent.']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Oops! Something went wrong. Please try again later.']);
    }
    
} else {
    // Not a POST request
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'There was a problem with your submission, please try again.']);
}
?>
