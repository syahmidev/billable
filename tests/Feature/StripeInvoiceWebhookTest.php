<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\StripeEventStatus;
use App\Models\StripeEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class StripeInvoiceWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const WEBHOOK_SECRET = 'whsec_test_secret';

    public function test_it_logs_and_ignores_unsupported_stripe_events(): void
    {
        $payload = $this->stripePayload('evt_charge_succeeded', 'charge.succeeded', [
            'id' => 'ch_test',
            'object' => 'charge',
        ]);

        $this->postSignedWebhook($payload)
            ->assertOk()
            ->assertSee('Event ignored.');

        $this->assertDatabaseHas('stripe_events', [
            'stripe_event_id' => 'evt_charge_succeeded',
            'type' => 'charge.succeeded',
            'status' => StripeEventStatus::Ignored->value,
            'last_error' => 'Unsupported event type.',
        ]);
    }

    public function test_it_does_not_reprocess_already_handled_stripe_events(): void
    {
        $payload = $this->stripePayload('evt_missing_metadata', 'payment_intent.succeeded', [
            'id' => 'pi_missing_metadata',
            'object' => 'payment_intent',
            'metadata' => [],
        ]);

        $this->postSignedWebhook($payload)
            ->assertOk()
            ->assertSee('Missing metadata.');

        $this->postSignedWebhook($payload)
            ->assertOk()
            ->assertSee('Event already handled.');

        $this->assertSame(1, StripeEvent::where('stripe_event_id', 'evt_missing_metadata')->count());
        $this->assertDatabaseHas('stripe_events', [
            'stripe_event_id' => 'evt_missing_metadata',
            'status' => StripeEventStatus::Ignored->value,
            'attempts' => 1,
            'payment_intent_id' => 'pi_missing_metadata',
            'last_error' => 'Missing metadata.',
        ]);
    }

    public function test_it_rejects_invalid_stripe_webhook_signatures(): void
    {
        config(['cashier.webhook.secret' => self::WEBHOOK_SECRET]);

        $payload = $this->stripePayload('evt_bad_signature', 'charge.succeeded', [
            'id' => 'ch_test',
            'object' => 'charge',
        ]);

        $this->call(
            'POST',
            '/stripe/invoice-webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_STRIPE_SIGNATURE' => 't='.time().',v1=invalid',
            ],
            $payload,
        )
            ->assertStatus(400)
            ->assertSee('Invalid signature.');

        $this->assertDatabaseMissing('stripe_events', [
            'stripe_event_id' => 'evt_bad_signature',
        ]);
    }

    private function postSignedWebhook(string $payload): TestResponse
    {
        config(['cashier.webhook.secret' => self::WEBHOOK_SECRET]);

        $timestamp = time();
        $signature = hash_hmac('sha256', "{$timestamp}.{$payload}", self::WEBHOOK_SECRET);

        return $this->call(
            'POST',
            '/stripe/invoice-webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
            ],
            $payload,
        );
    }

    private function stripePayload(string $eventId, string $type, array $object): string
    {
        return (string) json_encode([
            'id' => $eventId,
            'object' => 'event',
            'type' => $type,
            'data' => [
                'object' => $object,
            ],
        ]);
    }
}
