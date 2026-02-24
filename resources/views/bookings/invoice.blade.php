<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $booking->invoice_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1f2937;
            margin: 30px;
        }

        .header {
            margin-bottom: 20px;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }

        .muted {
            color: #6b7280;
            font-size: 11px;
        }

        .section {
            margin-top: 16px;
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f9fafb;
        }

        .text-right {
            text-align: right;
        }

        .total {
            font-size: 15px;
            font-weight: bold;
        }

        .list {
            margin: 6px 0 0 18px;
            padding: 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <p class="title">INVOICE</p>
        <p class="muted">Invoice No: {{ $booking->invoice_number }}</p>
        <p class="muted">Issued: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <div class="section">
        <strong>Customer</strong><br>
        {{ $booking->guest_name }}<br>
        {{ $booking->guest_email }}<br>
        {{ $booking->guest_phone }}
    </div>

    <div class="section">
        <strong>Booking</strong><br>
        Booking Code: {{ $booking->booking_code }}<br>
        Service: {{ $booking->product_name }}<br>
        Service Date: {{ $booking->service_date->format('d M Y') }} {{ \Carbon\Carbon::parse($booking->service_time)->format('H:i') }}
        @if($booking->service_type === 'tour')
            <br>
            Meeting Point Confirmed: {{ $booking->meeting_point_confirmed ? 'Yes' : 'No' }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $booking->product_name }} (Booking {{ $booking->booking_code }}) - Service Total</td>
                <td class="text-right">{{ $booking->quantity }}</td>
                <td class="text-right">{{ $booking->currency }} {{ number_format($booking->total_price - ($booking->add_ons_total ?? 0), 2) }}</td>
            </tr>
            @if(($booking->add_ons_total ?? 0) > 0)
                <tr>
                    <td>Add-ons</td>
                    <td class="text-right">-</td>
                    <td class="text-right">{{ $booking->currency }} {{ number_format($booking->add_ons_total, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td colspan="2" class="text-right total">Total</td>
                <td class="text-right total">{{ $booking->currency }} {{ number_format($booking->total_price, 2) }}</td>
            </tr>
            @if($booking->payment_status === 'refunded')
            <tr>
                <td colspan="2" class="text-right">Refunded</td>
                <td class="text-right">-{{ $booking->currency }} {{ number_format($booking->refund_amount ?? $booking->total_price, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    @if(!empty($booking->selected_add_ons))
        <div class="section">
            <strong>Selected Add-ons</strong>
            <ul class="list">
                @foreach($booking->selected_add_ons as $option)
                    <li>{{ $option['name'] ?? '-' }} ({{ $booking->currency }} {{ number_format((float) ($option['price'] ?? 0), 2) }})</li>
                @endforeach
            </ul>
        </div>
    @endif

    <p class="muted" style="margin-top: 12px;">
        Payment Status: {{ strtoupper($booking->payment_status) }}
        @if($booking->paid_at)
            | Paid At: {{ $booking->paid_at->format('d M Y H:i') }}
        @endif
        @if($booking->refunded_at)
            | Refunded At: {{ $booking->refunded_at->format('d M Y H:i') }}
        @endif
    </p>
</body>

</html>
