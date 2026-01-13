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
    Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->decimal('amount', 10, 2);
    $table->enum('method', ['cash', 'card', 'online', 'bank_transfer']);
    $table->string('gateway')->nullable(); // stripe, razorpay, paypal
    $table->string('transaction_id')->nullable();
    $table->string('gateway_transaction_id')->nullable();
    $table->enum('status', ['pending', 'completed', 'failed', 'refunded', 'partially_refunded']);
    $table->json('gateway_response')->nullable();
    $table->timestamp('paid_at')->nullable();
    $table->text('notes')->nullable();
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
