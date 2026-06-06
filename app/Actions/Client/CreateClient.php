<?php

declare(strict_types=1);

namespace App\Actions\Client;

use App\Actions\Activity\RecordActivity;
use App\Enums\ActivityType;
use App\Models\Client;
use App\Models\User;
use App\Services\PlanLimitsService;

class CreateClient
{
    public function __construct(
        private readonly RecordActivity $activity,
        private readonly PlanLimitsService $planLimits,
    ) {}

    public function handle(array $data, ?User $actor = null): Client
    {
        $this->planLimits->enforceClientLimit();

        $client = Client::create($data);

        $this->activity->handle(
            type: ActivityType::ClientCreated,
            description: "{$client->name} was added as a client.",
            actor: $actor,
            subject: $client,
        );

        return $client;
    }
}
