<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stripe_events', function (Blueprint $table): void {
            $table->id();
            $table->string('stripe_event_id')->unique();
            $table->string('type')->index();
            $table->string('status')->default('received')->index();
            $table->string('tenant_id')->nullable()->index();
            $table->string('invoice_id')->nullable()->index();
            $table->string('payment_intent_id')->nullable()->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->json('payload')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stripe_events');
    }
};
