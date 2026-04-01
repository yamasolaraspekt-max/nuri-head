<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToPersonalNotesTable extends Migration
{
    public function up()
    {
        Schema::table('personal_notes', function (Blueprint $table) {
            $table->index(['reminder_date', 'reminder_time']);
            $table->index('is_notified');
        });
    }

    public function down()
    {
        Schema::table('personal_notes', function (Blueprint $table) {
            $table->dropIndex(['reminder_date', 'reminder_time']);
            $table->dropIndex(['is_notified']);
        });
    }
}
