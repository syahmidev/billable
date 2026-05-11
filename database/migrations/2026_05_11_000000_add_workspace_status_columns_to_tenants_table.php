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
        Schema::table('tenants', function (Blueprint $table): void {
            $table->string('name')->nullable();
            $table->boolean('is_suspended')->default(false);
        });

        DB::table('tenants')
            ->select(['id', 'data'])
            ->orderBy('id')
            ->get()
            ->each(function (object $tenant): void {
                $data = $this->decodeData($tenant->data);
                $name = $data['name'] ?? null;
                $isSuspended = (bool) ($data['is_suspended'] ?? false);

                unset($data['name'], $data['is_suspended']);

                DB::table('tenants')
                    ->where('id', $tenant->id)
                    ->update([
                        'name' => $name,
                        'is_suspended' => $isSuspended,
                        'data' => $data === [] ? null : json_encode($data),
                    ]);
            });
    }

    public function down(): void
    {
        DB::table('tenants')
            ->select(['id', 'name', 'is_suspended', 'data'])
            ->orderBy('id')
            ->get()
            ->each(function (object $tenant): void {
                $data = $this->decodeData($tenant->data);
                $data['name'] = $tenant->name;
                $data['is_suspended'] = (bool) $tenant->is_suspended;

                DB::table('tenants')
                    ->where('id', $tenant->id)
                    ->update([
                        'data' => json_encode($data),
                    ]);
            });

        Schema::table('tenants', function (Blueprint $table): void {
            $table->dropColumn(['name', 'is_suspended']);
        });
    }

    private function decodeData(mixed $data): array
    {
        if (is_array($data)) {
            return $data;
        }

        if (! is_string($data) || $data === '') {
            return [];
        }

        $decoded = json_decode($data, true);

        return is_array($decoded) ? $decoded : [];
    }
};
