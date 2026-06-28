<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
       'api/reminder/*/status',
         'api/due-personal-notes',
        'fusion/webhook/ajax',
        'ai/chats/*/message',
        'ids/search/callback',
        '/ids/receive',
        '/ids/callback', 
        'ids/callback',
        'offers/document/presence/leave',
        'admin/supplier-connectors/*/return',
        'admin/offers/folders/*/supplier/*/return',



    ];
}
