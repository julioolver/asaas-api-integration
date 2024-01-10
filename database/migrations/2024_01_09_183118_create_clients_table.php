<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const MAX_LENGTH_CPF_CNPJ = 14;
    const MAX_LENGTH_PHONE_NUMBER = 15;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('document_number', self::MAX_LENGTH_CPF_CNPJ);
            $table->stgring('phone', self::MAX_LENGTH_PHONE_NUMBER)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
