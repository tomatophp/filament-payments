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
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('alias')->nullable();
            $table->boolean('status')->default(1)->comment('0=>disable, 1=>enable');
            $table->text('gateway_parameters')->nullable();
            $table->text('supported_currencies')->nullable();
            $table->boolean('crypto')->default(0)->comment('0: fiat currency, 1: crypto currency');
            $table->text('configurations')->nullable();
            $table->text('description')->nullable();
            $table->unsignedMediumInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
