<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $columns = [
        'company_name', 'contact_mobile', 'whatsapp_number', 'office_hours', 'map_embed_url',
        'hero_script', 'hero_title', 'hero_subtitle', 'hero_image', 'hero_video_url',
        'why_title', 'why_description', 'why_image', 'stat_years', 'stat_travelers', 'stat_destinations',
        'meta_title', 'meta_description', 'google_analytics_id', 'facebook_pixel_id',
    ];

    public function up(): void
    {
        Schema::table('page_settings', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('id');
            $table->string('contact_mobile')->nullable()->after('contact_phone');
            $table->string('whatsapp_number')->nullable()->after('contact_mobile');
            $table->string('office_hours')->nullable()->after('contact_address');
            $table->text('map_embed_url')->nullable()->after('office_hours');
            $table->string('hero_script')->nullable();
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('hero_video_url')->nullable();
            $table->string('why_title')->nullable();
            $table->text('why_description')->nullable();
            $table->string('why_image')->nullable();
            $table->unsignedInteger('stat_years')->nullable();
            $table->unsignedInteger('stat_travelers')->nullable();
            $table->unsignedInteger('stat_destinations')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('google_analytics_id')->nullable();
            $table->string('facebook_pixel_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('page_settings', function (Blueprint $table) {
            $table->dropColumn($this->columns);
        });
    }
};
