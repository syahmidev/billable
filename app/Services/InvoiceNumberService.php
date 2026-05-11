<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;

class InvoiceNumberService
{
    private const SEQUENCE_NAME = 'default';

    public function next(): string
    {
        DB::table('invoice_number_sequences')->insertOrIgnore([
            'name' => self::SEQUENCE_NAME,
            'last_number' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sequence = DB::table('invoice_number_sequences')
            ->where('name', self::SEQUENCE_NAME)
            ->lockForUpdate()
            ->first();

        $nextNumber = ((int) $sequence->last_number) + 1;

        DB::table('invoice_number_sequences')
            ->where('name', self::SEQUENCE_NAME)
            ->update([
                'last_number' => $nextNumber,
                'updated_at' => now(),
            ]);

        return $this->format($nextNumber);
    }

    private function format(int $number): string
    {
        return 'INV-'.str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }
}
