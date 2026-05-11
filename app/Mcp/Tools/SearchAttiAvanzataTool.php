<?php

declare(strict_types = 1);

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Structured search on Normattiva acts using advanced filters like dates, class, title, and full text.')]
class SearchAttiAvanzataTool extends Tool {
    /** Handle the tool request. */
    public function handle(Request $request) : Response {
        $validated = $request->validate([
            'denominazioneAtto'                   => ['nullable', 'string', 'max:100'],
            'titoloRicerca'                       => ['nullable', 'string', 'max:500'],
            'testoRicerca'                        => ['nullable', 'string', 'max:500'],
            'orderType'                           => ['nullable', 'string', 'in:recente,rilevante'],
            'dataInizioEmanazione'                => ['nullable', 'date_format:Y-m-d'],
            'dataFineEmanazione'                  => ['nullable', 'date_format:Y-m-d'],
            'dataInizioPubProvvedimento'          => ['nullable', 'date_format:Y-m-d'],
            'dataFinePubProvvedimento'            => ['nullable', 'date_format:Y-m-d'],
            'vigenza'                             => ['nullable', 'date_format:Y-m-d'],
            'classeProvvedimento'                 => ['nullable', 'string', 'max:50'],
            'paginazione'                         => ['nullable', 'array'],
            'paginazione.paginaCorrente'          => ['nullable', 'integer', 'min:1'],
            'paginazione.numeroElementiPerPagina' => ['nullable', 'integer', 'min:1', 'max:100'],
            'filtriMap'                           => ['nullable', 'array'],
        ]);

        if (!isset($validated['titoloRicerca']) && !isset($validated['testoRicerca']) && !isset($validated['denominazioneAtto'])) {
            return Response::error('Provide at least one between denominazioneAtto, titoloRicerca, or testoRicerca.');
        }

        $payload = array_replace_recursive([
            'paginazione' => [
                'paginaCorrente'          => 1,
                'numeroElementiPerPagina' => 10,
            ],
        ], $validated);

        return Http::normattiva()
            ->post('ricerca/avanzata', $payload)
            ->throw()
            ->json();
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema) : array {
        return [
            'denominazioneAtto'          => $schema->string()->description('Act denomination code or label, for example LEGGE or DECRETO.'),
            'titoloRicerca'              => $schema->string()->description('Query text on act title.'),
            'testoRicerca'               => $schema->string()->description('Query text on full act text.'),
            'orderType'                  => $schema->string()->description('Sort order for results.')->enum(['recente', 'rilevante'])->default('recente'),
            'dataInizioEmanazione'       => $schema->string()->description('Start date for issuance filter in format Y-m-d.'),
            'dataFineEmanazione'         => $schema->string()->description('End date for issuance filter in format Y-m-d.'),
            'dataInizioPubProvvedimento' => $schema->string()->description('Start publication date filter in format Y-m-d.'),
            'dataFinePubProvvedimento'   => $schema->string()->description('End publication date filter in format Y-m-d.'),
            'vigenza'                    => $schema->string()->description('Date for validity filter in format Y-m-d.'),
            'classeProvvedimento'        => $schema->string()->description('Provision class code.'),
            'paginazione'                => $schema->object()->description('Pagination object with paginaCorrente and numeroElementiPerPagina.'),
            'filtriMap'                  => $schema->object()->description('Optional map of facet filters.'),
        ];
    }
}
