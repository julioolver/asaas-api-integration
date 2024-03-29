<?php

use App\Enums\PaymentStatus;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount');
            $table->string('method');
            $table->string('status')->default(PaymentStatus::PENDING);
            $table->string('gateway_payment_id')->nullable();
            $table->string('bank_url')->nullable();
            $table->string('invoice_url')->nullable();
            $table->text('pix_data')->nullable();
            $table->string('card_authorization_number')->nullable();
            $table->string('nosso_numero')->nullable();
            $table->string('bar_code')->nullable();
            $table->string('identification_field')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
