<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contact Message</title>
</head>

<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">New Contact Message</h2>
    <p style="margin-top: 0; color: #6b7280;">A new message has been sent from the contact form.</p>

    <div style="border:1px solid #e5e7eb; border-radius:8px; padding:16px; margin-top:16px;">
        <p><strong>Name:</strong> {{ $payload['name'] }}</p>
        <p><strong>Email:</strong> {{ $payload['email'] }}</p>
        <p><strong>Subject:</strong> {{ $payload['subject'] }}</p>
        <p><strong>Message:</strong></p>
        <p style="white-space: pre-line;">{{ $payload['message'] }}</p>
    </div>
</body>

</html>
