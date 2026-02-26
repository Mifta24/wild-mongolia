<?php

return [
    'check_in_window' => [
        'days_before' => (int) env('BOOKING_CHECKIN_WINDOW_DAYS_BEFORE', 1),
        'days_after' => (int) env('BOOKING_CHECKIN_WINDOW_DAYS_AFTER', 1),
    ],
];
