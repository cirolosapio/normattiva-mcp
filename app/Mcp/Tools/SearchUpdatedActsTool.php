<?php

declare(strict_types = 1);

namespace App\Mcp\Tools;

use App\Mcp\Resources\SearchUpdatedActsApp;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\RendersApp;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly(true)]
#[IsDestructive(false)]
#[IsIdempotent(false)]
#[Title('Recupera atti aggiornati')]
#[Description('Retrieve Normattiva acts updated within a date-time interval')]
// #[RendersApp(resource: SearchUpdatedActsApp::class)]
class SearchUpdatedActsTool extends Tool {
    public function handle(Request $request) : Response {
        $validated = $request->validate([
            'dataInizioAggiornamento' => ['required', 'date'],
            'dataFineAggiornamento'   => ['required', 'date', 'after_or_equal:dataInizioAggiornamento'],
        ]);

        return Response::json(
            Http::normattiva()
                ->post('ricerca/aggiornati', $validated)
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
            'dataInizioAggiornamento' => $schema->string()
                ->description('Data e ora di inizio dell\'intervallo di aggiornamento, in formato ISO-8601')
                ->required(),
            'dataFineAggiornamento' => $schema->string()
                ->description('Data e ora di fine dell\'intervallo di aggiornamento, in formato ISO-8601')
                ->required(),
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
                ->description('Elenco degli atti trovati nell\'intervallo richiesto')
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
