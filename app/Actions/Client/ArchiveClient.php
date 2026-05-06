<?php

declare(strict_types=1);

namespace App\Actions\Client;

use App\Actions\Activity\RecordActivity;
use App\Enums\ActivityType;
use App\Models\Client;
use App\Models\User;

class ArchiveClient
{
    public function __construct(private readonly RecordActivity $activity) {}

    public function handle(Client $client, ?User $actor = null): void
    {
        $clientName = $client->name;
        $clientId = $client->id;

        $client->delete();

        $this->activity->handle(
            type: ActivityType::ClientArchived,
            description: "{$clientName} was archived.",
            actor: $actor,
            metadata: ['client_id' => $clientId],
        );
    }
}
