<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_data', function (Blueprint $table) {
            $table->id();
            $table->string('seoable_type');
            $table->unsignedBigInteger('seoable_id');
            $table->string('title', 70)->nullable();
            $table->string('description', 160)->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('robots_directive', 50)->default('index,follow');
            $table->string('focus_keyword', 100)->nullable();
            $table->boolean('og_use_hero_image')->default(false);
            $table->json('open_graph')->nullable();
            $table->boolean('twitter_use_hero_image')->default(false);
            $table->json('twitter_card')->nullable();
            $table->string('schema_type')->nullable();
            $table->json('schema_data')->nullable();
            $table->timestamp('content_updated_at')->nullable();
            $table->text('update_notes')->nullable();
            $table->json('faq_pairs')->nullable();
            $table->timestamps();

            $table->unique(['seoable_type', 'seoable_id']);
            $table->index('seoable_type');
            $table->index('seoable_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_data');
    }
};
