<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_access', function (Blueprint $table) {
            $table->id();

            // RELATION TO USERS TABLE
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // MENU / ACCESS
            $table->string('menu_access');

            $table->boolean('can_create')->default(false);
            $table->boolean('can_update')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->boolean('can_download')->default(false);
            $table->boolean('can_view_detail')->default(false);
            $table->boolean('can_monitor')->default(false);

            // INFORMATIONS
            $table->foreignId('created_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_access');
    }
};