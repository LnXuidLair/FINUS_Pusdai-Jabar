<?php

return [
    // Beams hanya aktif jika kredensial tersedia. Push browser butuh izin pengguna.
    'beams' => [
        'instance_id' => env('PUSHER_BEAMS_INSTANCE_ID'),
        'secret_key' => env('PUSHER_BEAMS_SECRET_KEY'),
    ],
    'poll_seconds' => 20,
];
