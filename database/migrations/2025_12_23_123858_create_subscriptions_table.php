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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('subscription_plans')->onDelete('restrict');
            $table->enum('status', ['active', 'cancelled', 'expired', 'trial'])->default('trial');
            $table->date('current_period_start');
            $table->date('current_period_end');
            $table->date('next_billing_date')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            // Indexes para performance
            $table->index('business_id');
            $table->index('plan_id');
            $table->index('status');
            $table->index(['business_id', 'status']); // Buscar suscripción activa de un negocio
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
