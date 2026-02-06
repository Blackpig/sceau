<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sceau_seo_data', function (Blueprint $table) {
            $table->id();

            // Morph relationship
            $table->string('seoable_type');
            $table->unsignedBigInteger('seoable_id');

            // Basic SEO fields (translatable)
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('robots_directive', 50)->default('index,follow');
            $table->json('focus_keyword')->nullable();

            // Open Graph fields
            $table->boolean('og_use_hero_image')->default(false);
            $table->json('og_title')->nullable();
            $table->json('og_description')->nullable();
            $table->json('og_image')->nullable();
            $table->string('og_type')->nullable();
            $table->string('og_site_name')->nullable();
            $table->string('og_locale')->nullable();

            // Twitter Card fields
            $table->json('twitter_title')->nullable();
            $table->json('twitter_description')->nullable();
            $table->string('twitter_card_type')->nullable();
            $table->string('twitter_site')->nullable();
            $table->string('twitter_creator')->nullable();

            // Schema.org structured data
            $table->string('schema_type')->nullable();
            $table->json('schema_data')->nullable();

            // Content tracking
            $table->timestamp('content_updated_at')->nullable();
            $table->text('update_notes')->nullable();

            $table->timestamps();

            $table->unique(['seoable_type', 'seoable_id']);
            $table->index('seoable_type');
            $table->index('seoable_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sceau_seo_data');
    }
};
