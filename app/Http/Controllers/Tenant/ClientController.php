<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Actions\Client\ArchiveClient;
use App\Actions\Client\CreateClient;
use App\Actions\Client\UpdateClient;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\SaveClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Client::class, 'client');
    }

    public function index(Request $request): Response
    {
        $clients = Client::query()
            ->when($request->search, fn ($q, $s) => $q
                ->where('name', 'ilike', "%{$s}%")
                ->orWhere('email', 'ilike', "%{$s}%")
                ->orWhere('company', 'ilike', "%{$s}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Tenant/Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tenant/Clients/Create');
    }

    public function store(SaveClientRequest $request, CreateClient $action): RedirectResponse
    {
        $action->handle($request->validated(), $request->user());

        return redirect()->route('tenant.clients.index')->with('success', 'Client created.');
    }

    public function show(Client $client): Response
    {
        return Inertia::render('Tenant/Clients/Show', [
            'client' => $client,
            'invoices' => $client->invoices()->with('items')->latest()->get()->map(fn ($inv) => [
                'id' => $inv->id,
                'invoice_number' => $inv->invoice_number,
                'status' => $inv->status,
                'issue_date' => $inv->issue_date->format('M d, Y'),
                'due_date' => $inv->due_date->format('M d, Y'),
                'total' => (float) $inv->total,
            ]),
        ]);
    }

    public function edit(Client $client): Response
    {
        return Inertia::render('Tenant/Clients/Edit', [
            'client' => $client,
        ]);
    }

    public function update(SaveClientRequest $request, Client $client, UpdateClient $action): RedirectResponse
    {
        $action->handle($client, $request->validated(), $request->user());

        return redirect()->route('tenant.clients.show', $client)->with('success', 'Client updated.');
    }

    public function destroy(Request $request, Client $client, ArchiveClient $action): RedirectResponse
    {
        $action->handle($client, $request->user());

        return redirect()->route('tenant.clients.index')->with('success', 'Client archived.');
    }
}
