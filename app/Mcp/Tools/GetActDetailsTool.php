<?php

declare(strict_types = 1);

namespace App\Mcp\Tools;

use App\Mcp\Resources\ActDetailsApp;
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

#[IsDestructive(false)]
#[IsIdempotent(false)]
#[Title('Recupera dettaglio atto')]
#[Description('Recupera il dettaglio di un atto utilizzando parametri di redazione come codiceRedazionale, data GU, articolo e data di validità')]
#[RendersApp(resource: ActDetailsApp::class)]
class GetActDetailsTool extends Tool {
    public function handle(Request $request) : Response {
        $validated = $request->validate([
            'dataGU'            => ['required', 'date_format:Y-m-d'],
            'codiceRedazionale' => ['required', 'string', 'max:32'],
            'idArticolo'        => ['nullable', 'integer', 'min:0'],
            'sottoArticolo'     => ['nullable', 'integer', 'min:0'],
            'sottoArticolo1'    => ['nullable', 'integer', 'min:0'],
            'dataVigenza'       => ['nullable', 'date_format:Y-m-d'],
            'idGruppo'          => ['nullable', 'integer', 'min:0'],
            'progressivo'       => ['nullable', 'integer', 'min:0'],
            'versione'          => ['nullable', 'integer', 'min:0'],
        ]);

        return Response::json(
            Http::normattiva()
                ->post('atto/dettaglio-atto', $validated)
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
            'dataGU' => $schema->string()
                ->description('Data della Gazzetta Ufficiale in formato Y-m-d')
                ->required(),
            'codiceRedazionale' => $schema->string()
                ->description('Codice di redazione Normattiva, ad esempio 088G0458')
                ->required(),
            'idArticolo'     => $schema->integer()->description('Identificativo articolo opzionale'),
            'sottoArticolo'  => $schema->integer()->description('Identificativo sottarticolo opzionale'),
            'sottoArticolo1' => $schema->integer()->description('Identificativo sottarticolo annidato opzionale'),
            'dataVigenza'    => $schema->string()->description('Data di vigenza opzionale in formato Y-m-d'),
            'idGruppo'       => $schema->integer()->description('Identificativo gruppo opzionale'),
            'progressivo'    => $schema->integer()->description('Identificativo progressivo opzionale'),
            'versione'       => $schema->integer()->description('Numero di versione opzionale'),
        ];
    }

    /**
     * Get the tool's output schema.
     *
     * @return array<string, JsonSchema>
     */
    public function outputSchema(JsonSchema $schema) : array {
        return [
            'code' => $schema->integer()
                ->nullable()
                ->description('Codice di errore applicativo restituito dal backend, se presente'),
            'message' => $schema->string()
                ->nullable()
                ->description('Messaggio di errore o informazione generale'),
            'success' => $schema->boolean()
                ->description('Indica se la chiamata e stata elaborata con successo')
                ->required(),
            'data' => $schema->object([
                'atto' => $schema->object([
                    'titolo'                       => $schema->string()->description('Titolo dell\'atto'),
                    'sottoTitolo'                  => $schema->string()->nullable()->description('Sottotitolo dell\'atto'),
                    'articoloHtml'                 => $schema->string()->description('Contenuto articolo in HTML'),
                    'tipoProvvedimentoDescrizione' => $schema->string()->description('Descrizione del tipo di provvedimento'),
                    'tipoProvvedimentoCodice'      => $schema->string()->description('Codice del tipo di provvedimento'),
                    'annoProvvedimento'            => $schema->integer()->description('Anno del provvedimento'),
                    'numeroProvvedimento'          => $schema->integer()->description('Numero del provvedimento'),
                    'dataPubblicazioneInGazzetta'  => $schema->string()->nullable()->description('Data di pubblicazione in Gazzetta Ufficiale'),
                    'articoloDataInizioVigenza'    => $schema->string()->nullable()->description('Data inizio vigenza dell\'articolo'),
                    'articoloDataFineVigenza'      => $schema->string()->nullable()->description('Data fine vigenza dell\'articolo'),
                    'testoInVigore'                => $schema->string()->nullable()->description('Indicazione testuale sullo stato di vigenza'),
                ])->description('Dettaglio dell\'atto richiesto')->required(),
                'message' => $schema->string()
                    ->nullable()
                    ->description('Messaggio informativo associato al contenuto data'),
            ])->description('Payload di risposta del dettaglio atto')->required(),
        ];
    }
}
