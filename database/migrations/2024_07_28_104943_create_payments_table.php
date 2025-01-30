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
            // Basic payment details
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('model_type')->nullable();

            // Payment information
            $table->unsignedBigInteger('method_id')->nullable();
            $table->string('method_name')->nullable();
            $table->string('method_code')->nullable();
            $table->string('method_currency')->nullable();

            // Financial details
            $table->decimal('amount', 28, 8)->default(0);
            $table->decimal('charge', 28, 8)->default(0);
            $table->decimal('rate', 28, 8)->default(1);
            $table->decimal('final_amount', 28, 8)->default(0);

            // Additional payment information
            $table->text('detail')->nullable();
            $table->string('trx')->unique();

            // Status and retry information
            $table->string('payment_try')->default(0);
            $table->smallInteger('status')->default(0)->comment('0=>pending, 1=>success, 2=>cancel');
            $table->boolean('from_api')->default(0);

            // URLs and feedback
            $table->string('admin_feedback')->nullable();
            $table->string('success_url')->nullable();
            $table->string('failed_url')->nullable();

            // JSON fields for various information
            $table->json('customer')->nullable();
            $table->json('shipping_info')->nullable();
            $table->json('billing_info')->nullable();
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
