<?php

// Jitsi Videocall-Integration (Feature-Flag; Server existiert noch nicht -> default aus).
// Domain/Secrets kommen aus der .env. Ohne JITSI_ENABLED=true sind alle Routen/Buttons inaktiv.
return [
    'enabled' => (bool) env('JITSI_ENABLED', false),

    'domain' => env('JITSI_DOMAIN', 'meet.solaraspekt.de'),

    // JWT (HS256) nur wenn ausdrücklich aktiviert; sonst offener Raum ohne Token.
    'jwt_enabled' => (bool) env('JITSI_JWT_ENABLED', false),
    'app_id' => env('JITSI_APP_ID'),
    'app_secret' => env('JITSI_APP_SECRET'),

    // Gültigkeit des signierten Gast-Links (Minuten).
    'guest_link_ttl_minutes' => (int) env('JITSI_GUEST_TTL', 240),
];
