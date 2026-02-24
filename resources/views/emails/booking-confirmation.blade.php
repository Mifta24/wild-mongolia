<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Booking Confirmation</title>
</head>

<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">Booking Confirmed 🎉</h2>
    <p style="margin-top: 0; color: #6b7280;">Thank you for booking with us.</p>

    <div style="border:1px solid #e5e7eb; border-radius:8px; padding:16px; margin-top:16px;">
        <p><strong>Booking Code:</strong> {{ $booking->booking_code }}</p>
        <p><strong>Service:</strong> {{ $booking->product_name }}</p>
        <p><strong>Date & Time:</strong> {{ $booking->service_date->format('d M Y') }} {{ \Carbon\Carbon::parse($booking->service_time)->format('H:i') }}</p>
        <p><strong>Total:</strong> {{ $booking->currency }} {{ number_format($booking->total_price, 2) }}</p>

        @if(($booking->add_ons_total ?? 0) > 0)
            <p><strong>Add-ons Total:</strong> {{ $booking->currency }} {{ number_format($booking->add_ons_total, 2) }}</p>
        @endif

        @if(!empty($booking->selected_add_ons))
            <p style="margin-bottom: 6px;"><strong>Selected Add-ons:</strong></p>
            <ul style="margin-top: 0;">
                @foreach($booking->selected_add_ons as $option)
                    <li>{{ $option['name'] ?? '-' }} ({{ $booking->currency }} {{ number_format((float) ($option['price'] ?? 0), 2) }})</li>
                @endforeach
            </ul>
        @endif

        @if($booking->service_type === 'tour')
            <p><strong>Meeting Point Confirmed:</strong> {{ $booking->meeting_point_confirmed ? 'Yes' : 'No' }}</p>
        @endif
    </div>

    <p style="margin-top: 16px; color:#6b7280;">If you need help, please contact our support team.</p>
</body>

</html>
