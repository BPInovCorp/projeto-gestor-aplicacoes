<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code', 2)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('contact_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('calendar_event_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color', 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('calendar_event_actions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('vat_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('value', 5, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('vat_number')->unique()->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code', 8)->nullable();
            $table->string('city')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
        Schema::dropIfExists('vat_rates');
        Schema::dropIfExists('calendar_event_actions');
        Schema::dropIfExists('calendar_event_types');
        Schema::dropIfExists('contact_roles');
        Schema::dropIfExists('countries');
    }
};