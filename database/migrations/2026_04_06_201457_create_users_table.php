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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
        
            $table->char('folio', 9)->unique(); // 🔥 login principal
            $table->enum('folio_type', ['ceneval', 'uady']);
        
            $table->string('email')->unique();
            $table->string('password');
        
            $table->enum('identity_type', ['curp', 'passport']);
            $table->string('identity_value')->unique();
        
            $table->string('phone')->unique(); // 🔥 requerido y único
        
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
        
            $table->string('status')->default('active');
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
