<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const MAX_LENGTH_CPF_CNPJ = 15;
    const MAX_LENGTH_PHONE_NUMBER = 30;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('document_number', self::MAX_LENGTH_CPF_CNPJ);
            $table->string('phone', self::MAX_LENGTH_PHONE_NUMBER)->nullable();
            $table->string('payment_gateway_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
