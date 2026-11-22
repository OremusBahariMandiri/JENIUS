<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('employee_id_number')->nullable();
            $table->string('action');         // login, logout, create, update, delete, view, restore
            $table->string('module')->nullable();      // nama modul (Customer, User, JO Contract, dsb)
            $table->string('model_name')->nullable();  // nama model/tabel
            $table->string('record_id')->nullable();   // id record yang diubah
            $table->text('description')->nullable();   // deskripsi singkat
            $table->json('old_values')->nullable();    // nilai lama (untuk update/delete)
            $table->json('new_values')->nullable();    // nilai baru (untuk create/update)
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->string('method', 10)->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('action');
            $table->index('module');
            $table->index('created_at');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
