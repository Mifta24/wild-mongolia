<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalMessagingService
{
    public function sendWhatsAppAutomation(Booking $booking, string $event, string $message): void
    {
        $payload = [
            'channel' => 'whatsapp',
            'event' => $event,
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'guest_name' => $booking->guest_name,
            'guest_phone' => $booking->guest_phone,
            'guest_email' => $booking->guest_email,
            'message' => $message,
            'chat_link' => $this->buildWhatsAppLink($booking, $message),
        ];

        $this->dispatch('whatsapp', $payload);
    }

    public function sendLineAutomation(Booking $booking, string $event, string $message): void
    {
        $payload = [
            'channel' => 'line',
            'event' => $event,
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'guest_name' => $booking->guest_name,
            'guest_phone' => $booking->guest_phone,
            'guest_email' => $booking->guest_email,
            'message' => $message,
            'chat_link' => $this->buildLineLink($booking, $message),
        ];

        $this->dispatch('line', $payload);
    }

    private function dispatch(string $provider, array $payload): void
    {
        $webhook = (string) config("services.{$provider}.webhook_url");

        if (filled($webhook)) {
            try {
                Http::timeout(8)->post($webhook, $payload)->throw();
            } catch (\Throwable $exception) {
                report($exception);
                Log::warning("{$provider} automation webhook failed.", [
                    'provider' => $provider,
                    'payload' => $payload,
                    'error' => $exception->getMessage(),
                ]);
            }

            return;
        }

        Log::info("{$provider} automation payload (no webhook configured).", [
            'provider' => $provider,
            'payload' => $payload,
        ]);
    }

    private function buildWhatsAppLink(Booking $booking, string $message): string
    {
        $number = preg_replace('/\D+/', '', (string) config('services.whatsapp.business_number'));

        if (blank($number)) {
            return '';
        }

        $text = urlencode($this->buildDefaultChatText($booking, $message));

        return "https://wa.me/{$number}?text={$text}";
    }

    private function buildLineLink(Booking $booking, string $message): string
    {
        $text = urlencode($this->buildDefaultChatText($booking, $message));

        return "https://line.me/R/msg/text/?{$text}";
    }

    private function buildDefaultChatText(Booking $booking, string $message): string
    {
        return "[{$booking->booking_code}] {$message}";
    }
}
