<?php

declare(strict_types = 1);

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[IsDestructive(false)]
#[IsIdempotent(false)]
#[Title('Recupera atti avanzati')]
#[Description('Structured search on Normattiva acts using advanced filters like dates, class, title, and full text')]
class SearchAttiAvanzataTool extends Tool {
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
            return Response::error('Provide at least one between denominazioneAtto, titoloRicerca, or testoRicerca');
        }

        $payload = array_replace_recursive([
            'paginazione' => [
                'paginaCorrente'          => 1,
                'numeroElementiPerPagina' => 10,
            ],
        ], $validated);

        return Response::json(
            Http::normattiva()
                ->post('ricerca/avanzata', $payload)
                ->throw()
                ->json()
        );
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema) : array {
        return [
            'denominazioneAtto'          => $schema->string()->description('Codice o etichetta denominazione atto, per esempio LEGGE o DECRETO'),
            'titoloRicerca'              => $schema->string()->description('Testo di ricerca sul titolo dell\'atto'),
            'testoRicerca'               => $schema->string()->description('Testo di ricerca sul testo completo dell\'atto'),
            'orderType'                  => $schema->string()->description('Ordine di ordinamento dei risultati')->enum(['recente', 'rilevante'])->default('recente'),
            'dataInizioEmanazione'       => $schema->string()->description('Data di inizio per il filtro di emanazione nel formato Y-m-d'),
            'dataFineEmanazione'         => $schema->string()->description('Data di fine per il filtro di emanazione nel formato Y-m-d'),
            'dataInizioPubProvvedimento' => $schema->string()->description('Filtro data di inizio della pubblicazione nel formato Y-m-d'),
            'dataFinePubProvvedimento'   => $schema->string()->description('Filtro data di fine della pubblicazione nel formato Y-m-d'),
            'vigenza'                    => $schema->string()->description('Data per il filtro di vigenza nel formato Y-m-d'),
            'classeProvvedimento'        => $schema->string()->description('Codice della classe del provvedimento'),
            'paginazione'                => $schema->object()->description('Oggetto paginazione con paginaCorrente e numeroElementiPerPagina'),
            'filtriMap'                  => $schema->object()->description('Mappa facoltativa di filtri per facet'),
        ];
    }
}
