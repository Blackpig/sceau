<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sceau_seo_settings', function (Blueprint $table) {
            $table->id();

            // Site information
            $table->string('site_name')->nullable();
            $table->string('site_url')->nullable();

            // Contact information
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();

            // Address
            $table->string('address_street')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_region')->nullable();
            $table->string('address_postal_code')->nullable();
            $table->string('address_country', 2)->nullable();

            // LocalBusiness specific
            $table->string('price_range')->nullable();
            $table->json('opening_hours')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sceau_seo_settings');
    }
};
