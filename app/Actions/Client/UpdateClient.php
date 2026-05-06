<?php

declare(strict_types=1);

namespace App\Actions\Client;

use App\Actions\Activity\RecordActivity;
use App\Enums\ActivityType;
use App\Models\Client;
use App\Models\User;

class UpdateClient
{
    public function __construct(private readonly RecordActivity $activity) {}

    public function handle(Client $client, array $data, ?User $actor = null): Client
    {
        $client->update($data);

        $this->activity->handle(
            type: ActivityType::ClientUpdated,
            description: "{$client->name} was updated.",
            actor: $actor,
            subject: $client,
        );

        return $client;
    }
}
