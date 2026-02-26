<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Voucher {{ $booking->booking_code }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            margin: 0;
            padding: 24px;
            background: #f3f4f6;
        }

        .card {
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            overflow: hidden;
        }

        .header {
            background: #0d9488;
            color: #ffffff;
            padding: 16px 20px;
        }

        .header small {
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 10px;
            opacity: .9;
        }

        .header h1 {
            margin: 6px 0 0;
            font-size: 22px;
        }

        .content {
            padding: 20px;
        }

        .row {
            margin-bottom: 12px;
        }

        .label {
            color: #6b7280;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .value {
            font-size: 14px;
            font-weight: 600;
        }

        .token {
            font-family: monospace;
            font-size: 10px;
            word-wrap: break-word;
        }

        .qr {
            text-align: center;
            margin-top: 20px;
        }

        .qr img {
            width: 210px;
            height: 210px;
            border: 1px solid #d1d5db;
            padding: 8px;
            border-radius: 8px;
        }

        .note {
            margin-top: 18px;
            border: 1px solid #f59e0b;
            background: #fffbeb;
            color: #92400e;
            padding: 10px;
            border-radius: 8px;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="header">
            <small>Thailand Travel Voucher</small>
            <h1>{{ $booking->product_name }}</h1>
        </div>
        <div class="content">
            <div class="row">
                <div class="label">Booking Code</div>
                <div class="value">{{ $booking->booking_code }}</div>
            </div>
            <div class="row">
                <div class="label">Guest Name</div>
                <div class="value">{{ $booking->guest_name }}</div>
            </div>
            <div class="row">
                <div class="label">Service Date</div>
                <div class="value">{{ \Carbon\Carbon::parse($booking->service_date)->format('D, d M Y') }} {{ $booking->service_time }}</div>
            </div>
            <div class="row">
                <div class="label">Voucher Token</div>
                <div class="token">{{ $booking->voucher_token }}</div>
            </div>

            <div class="qr">
                <img src="{{ $qrImageUrl }}" alt="Voucher QR Code">
                <div style="margin-top: 8px; font-size: 10px; color: #6b7280; word-wrap: break-word;">
                    {{ $checkInUrl }}
                </div>
            </div>

            <div class="note">
                Present this voucher at check-in. This QR code can only be checked in once by authorized staff.
            </div>
        </div>
    </div>
</body>

</html>
