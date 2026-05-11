<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Actions\Client\ArchiveClient;
use App\Actions\Client\CreateClient;
use App\Actions\Client\UpdateClient;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\SaveClientRequest;
use App\Models\Client;
use App\Queries\Tenant\ClientInvoiceHistoryQuery;
use App\Queries\Tenant\ClientListingQuery;
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

    public function index(Request $request, ClientListingQuery $clients): Response
    {
        return Inertia::render('Tenant/Clients/Index', [
            'clients' => $clients->handle($request->only('search')),
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

    public function show(Client $client, ClientInvoiceHistoryQuery $invoiceHistory): Response
    {
        return Inertia::render('Tenant/Clients/Show', [
            'client' => $client,
            'invoices' => $invoiceHistory->handle($client),
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
