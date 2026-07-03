<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Jitsi-Videocall. Ein Call ist entweder:
 *  - ein Kunden-Call (customer_id → new_leads), oder
 *  - ein interner Call: 1:1 (peer_user_id → users) oder Gruppe (chat_group_id → chat_groups).
 * Genau EINES der drei ist gesetzt (Validierung im Service, kein DB-Zwang — F2/Erweiterung).
 * room_name ist nicht erratbar. Additiv, berührt keinen Bestand.
 *
 * Hinweis: „customer_id nullable + peer/group-Spalten" wurde in diese Create-Migration
 * gefaltet, weil sie noch nicht angewandt war (keine sofort redundante Alter-Migration).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_calls', function (Blueprint $table) {
            $table->id();
            // Kunden-Call (F4: new_leads, nicht customers). Nullable für interne Calls.
            $table->foreignId('customer_id')->nullable()->constrained('new_leads')->nullOnDelete();
            // Interner Call: 1:1 (peer) ODER Gruppe.
            $table->foreignId('peer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('chat_group_id')->nullable()->constrained('chat_groups')->nullOnDelete();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('room_name')->unique();
            $table->enum('status', ['created', 'active', 'ended'])->default('created');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_calls');
    }
};
