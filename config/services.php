<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'fusion_forms' => [
        'token' => env('FUSION_FORMS_TOKEN'),
    ],

    'fusion_forms' => [
        'token' => env('FUSION_WEBHOOK_TOKEN'),
    ],

    'myuplink' => [
        'client_id' => env('MYUPLINK_CLIENT_ID'),
        'client_secret' => env('MYUPLINK_CLIENT_SECRET'),
    ],

    'openweather' => [
        'key' => env('WEATHER_API_KEY'),
    ],

    'tomorrowio' => [
        'key' => env('DASHBOARD_KEY'),
    ],

    'rapidapi' => [
        'key' => env('RAPIDAPI_KEY'),
    ],

    'ollama' => [
        'host' => env('OLLAMA_HOST','http://127.0.0.1:11434'),
        'model' => env('OLLAMA_MODEL','llama3:instruct'),
        'embed_model' => env('OLLAMA_EMBED_MODEL', 'mxbai-embed-large'), 
    ],

    'overpass' => [
        'url' => env('OVERPASS_URL', 'https://overpass-api.de/api/interpreter'),
    ],
    'google' => [
        'maps_key' => env('GOOGLE_MAPS_KEY'),
    ],

    'newsapi' => [
        'key'  => env('NEWSAPI_KEY'),
        'slug' => env('NEWSAPI_GROUP_SLUG', 'solar-news'),
    ],

    'ids' => [
        'shop_url' => env('IDS_SHOP_URL', 'https://gconlineplus.de/ids.aspx'),
        'kndnr'    => env('IDS_KNDNR'),
        'username' => env('IDS_USERNAME'),
        'password' => env('IDS_PASSWORD'),
    ],




];
