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
#[Title('Ricerca atti semplice')]
#[Description('Ricerca full-text sugli atti di Normattiva con supporto opzionale per paginazione, ordinamento e filtri a faccette')]
class SearchActsSimpleTool extends Tool {
    public function handle(Request $request) : Response {
        $validated = $request->validate([
            'testoRicerca'                        => ['required', 'string', 'min:2', 'max:500'],
            'orderType'                           => ['nullable', 'string', 'in:recente,rilevante'],
            'paginazione'                         => ['nullable', 'array'],
            'paginazione.paginaCorrente'          => ['nullable', 'integer', 'min:1'],
            'paginazione.numeroElementiPerPagina' => ['nullable', 'integer', 'min:1', 'max:100'],
            'filtriMap'                           => ['nullable', 'array'],
        ]);

        $payload = array_replace_recursive([
            'paginazione' => [
                'paginaCorrente'          => 1,
                'numeroElementiPerPagina' => 10,
            ],
        ], $validated);

        return Response::json(
            Http::normattiva()
                ->post('ricerca/semplice', $payload)
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
            'testoRicerca' => $schema->string()
                ->description('Testo libero per la ricerca di atti normativi')
                ->required(),
            'orderType' => $schema->string()
                ->description('Ordine di ordinamento dei risultati, può essere "recente" o "rilevante"')
                ->enum(['recente', 'rilevante'])
                ->default('recente'),
            'paginazione' => $schema->object()
                ->description('Oggetto paginazione con paginaCorrente e numeroElementiPerPagina'),
            'filtriMap' => $schema->object()
                ->description('Mappa facoltativa di filtri per facet, ad esempio codice_tipo_provvedimento o anno_provvedimento'),
        ];
    }

    /**
     * Get the tool's output schema.
     *
     * @return array<string, JsonSchema>
     */
    public function outputSchema(JsonSchema $schema) : array {
        return [
            'listaAtti' => $schema->array()
                ->items($schema->object([
                    'codiceRedazionale'  => $schema->string()->description('Codice redazionale dell\'atto'),
                    'titoloAtto'         => $schema->string()->description('Titolo completo dell\'atto'),
                    'descrizioneAtto'    => $schema->string()->nullable()->description('Descrizione sintetica dell\'atto'),
                    'denominazioneAtto'  => $schema->string()->description('Denominazione del tipo atto'),
                    'dataGU'             => $schema->string()->description('Data della Gazzetta Ufficiale in formato Y-m-d'),
                    'numeroGU'           => $schema->string()->description('Numero della Gazzetta Ufficiale'),
                    'dataEmanazione'     => $schema->string()->nullable()->description('Data di emanazione in formato ISO-8601'),
                    'dataUltimaModifica' => $schema->string()->nullable()->description('Data ultima modifica in formato ISO-8601'),
                ]))
                ->description('Elenco degli atti restituiti dalla ricerca semplice')
                ->required(),
            'facetMap' => $schema->object()
                ->description('Mappa dei facet aggregati per categoria')
                ->required(),
            'numeroPagine'            => $schema->integer()->description('Numero totale di pagine disponibili')->required(),
            'numeroAttiTrovati'       => $schema->integer()->description('Numero totale di atti trovati')->required(),
            'paginaCorrente'          => $schema->integer()->description('Pagina corrente dei risultati')->required(),
            'numeroElementiPerPagina' => $schema->integer()->description('Numero di elementi restituiti per pagina')->required(),
            'message'                 => $schema->string()->nullable()->description('Messaggio informativo opzionale'),
            'numeroFileRicerca'       => $schema->integer()->description('Identificativo numerico della ricerca'),
        ];
    }
}
