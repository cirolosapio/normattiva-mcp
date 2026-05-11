<?php

declare(strict_types = 1);

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Full-text search on Normattiva acts with optional pagination, sorting, and facet filters.')]
class SearchAttiSempliceTool extends Tool {
    /** Handle the tool request. */
    public function handle(Request $request) : Response {
        $validated = $request->validate([
            'testoRicerca'                        => ['required', 'string', 'min:2', 'max:500'],
            'orderType'                           => ['nullable', 'string', 'in:recente,rilevante'],
            'paginazione'                         => ['nullable', 'array'],
            'paginazione.paginaCorrente'          => ['nullable', 'integer', 'min:1'],
            'paginazione.numeroElementiPerPagina' => ['nullable', 'integer', 'min:1', 'max:100'],
            'filtriMap'                           => ['nullable', 'array'],
        ]);

        $response = Http::normattiva()->post('/ricerca/semplice', $validated);

        return Response::json([
            'status' => $response->status(),
            'data'   => $response->json(),
        ]);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema) : array {
        return [
            'testoRicerca' => $schema->string()
                ->description('Free text query to search for legal acts.')
                ->required(),
            'orderType' => $schema->string()
                ->description('Sort order for results.')
                ->enum(['recente', 'rilevante'])
                ->default('recente'),
            'paginazione' => $schema->object()
                ->description('Pagination object with paginaCorrente and numeroElementiPerPagina.'),
            'filtriMap' => $schema->object()
                ->description('Optional map of facet filters, for example codice_tipo_provvedimento or anno_provvedimento.'),
        ];
    }
}
