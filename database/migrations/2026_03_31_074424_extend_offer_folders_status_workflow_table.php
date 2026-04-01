<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offer_folders', function (Blueprint $table) {
            if (!Schema::hasColumn('offer_folders', 'document_status')) {
                $table->string('document_status', 20)
                    ->default('offer')
                    ->after('status');
            }

            if (!Schema::hasColumn('offer_folders', 'offer_status')) {
                $table->string('offer_status', 40)
                    ->default('draft')
                    ->after('document_status');
            }

            if (!Schema::hasColumn('offer_folders', 'deal_status')) {
                $table->string('deal_status', 40)
                    ->nullable()
                    ->after('offer_status');
            }
        });

        /**
         * Backfill current data from old generic status
         */
        DB::table('offer_folders')
            ->select('id', 'status')
            ->orderBy('id')
            ->chunkById(200, function ($folders) {
                foreach ($folders as $folder) {
                    $oldStatus = strtolower((string) ($folder->status ?? 'draft'));

                    $documentStatus = 'offer';
                    $offerStatus = 'draft';
                    $dealStatus = null;

                    switch ($oldStatus) {
                        case 'draft':
                            $offerStatus = 'draft';
                            break;

                        case 'sent':
                            $offerStatus = 'sent';
                            break;

                        case 'negotiation':
                            $offerStatus = 'negotiation';
                            break;

                        case 'final':
                            /**
                             * old "final" was used before for completed/success
                             * map it to accepted for offer workflow
                             */
                            $offerStatus = 'accepted';
                            break;

                        case 'cancel':
                            $offerStatus = 'cancelled';
                            break;

                        default:
                            $offerStatus = 'draft';
                            break;
                    }

                    DB::table('offer_folders')
                        ->where('id', $folder->id)
                        ->update([
                            'document_status' => $documentStatus,
                            'offer_status' => $offerStatus,
                            'deal_status' => $dealStatus,
                        ]);
                }
            });

        /**
         * Optional sync from offer_details.document_status if available
         * This makes existing folders inherit the current document mode.
         */
        if (Schema::hasTable('offer_details')) {
            $rows = DB::table('offer_folders as f')
                ->leftJoin('offer_details as d', 'd.offer_folder_id', '=', 'f.id')
                ->select('f.id', 'd.document_status')
                ->get();

            foreach ($rows as $row) {
                $docStatus = strtolower((string) ($row->document_status ?? 'offer'));
                $docStatus = in_array($docStatus, ['offer', 'deal'], true) ? $docStatus : 'offer';

                DB::table('offer_folders')
                    ->where('id', $row->id)
                    ->update([
                        'document_status' => $docStatus,
                        'deal_status' => $docStatus === 'deal'
                            ? DB::raw("COALESCE(deal_status, 'open')")
                            : DB::raw('deal_status'),
                    ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('offer_folders', function (Blueprint $table) {
            if (Schema::hasColumn('offer_folders', 'deal_status')) {
                $table->dropColumn('deal_status');
            }

            if (Schema::hasColumn('offer_folders', 'offer_status')) {
                $table->dropColumn('offer_status');
            }

            if (Schema::hasColumn('offer_folders', 'document_status')) {
                $table->dropColumn('document_status');
            }
        });
    }
};