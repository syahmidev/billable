<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_number_sequences', function (Blueprint $table): void {
            $table->string('name')->primary();
            $table->unsignedBigInteger('last_number')->default(0);
            $table->timestamps();
        });

        DB::table('invoice_number_sequences')->insert([
            'name' => 'default',
            'last_number' => $this->lastIssuedInvoiceNumber(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_number_sequences');
    }

    private function lastIssuedInvoiceNumber(): int
    {
        return DB::table('invoices')
            ->pluck('invoice_number')
            ->map(function (string $invoiceNumber): int {
                preg_match('/^INV-(\d+)$/', $invoiceNumber, $matches);

                return (int) ($matches[1] ?? 0);
            })
            ->max() ?? 0;
    }
};
