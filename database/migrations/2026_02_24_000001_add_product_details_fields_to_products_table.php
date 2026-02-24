<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('gallery_images')->nullable()->after('image_url');
            $table->longText('itinerary')->nullable()->after('description');
            $table->json('add_ons')->nullable()->after('itinerary');
            $table->longText('cancellation_policy')->nullable()->after('add_ons');
            $table->string('meeting_point_name')->nullable()->after('cancellation_policy');
            $table->string('meeting_point_address')->nullable()->after('meeting_point_name');
            $table->decimal('meeting_point_lat', 10, 7)->nullable()->after('meeting_point_address');
            $table->decimal('meeting_point_lng', 10, 7)->nullable()->after('meeting_point_lat');
            $table->text('meeting_point_embed_url')->nullable()->after('meeting_point_lng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'gallery_images',
                'itinerary',
                'add_ons',
                'cancellation_policy',
                'meeting_point_name',
                'meeting_point_address',
                'meeting_point_lat',
                'meeting_point_lng',
                'meeting_point_embed_url',
            ]);
        });
    }
};
