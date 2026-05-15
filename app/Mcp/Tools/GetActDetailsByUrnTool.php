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
#[Title('Recupera dettaglio atto da URN')]
#[Description('Recupera il dettaglio di un atto da Normattiva utilizzando un URN NIR, ad esempio urn:nir:stato:legge:2001-12-28;448~art2')]
class GetActDetailsByUrnTool extends Tool {
    public function handle(Request $request) : Response {
        $validated = $request->validate([
            'urn' => ['required', 'string', 'starts_with:urn:'],
        ]);

        return Response::json(
            Http::normattiva()
                ->post('atto/dettaglio-atto-urn', $validated)
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
            'urn' => $schema->string()
                ->description('URN NIR che identifica l\'atto normativo o un articolo specifico, ad esempio urn:nir:stato:legge:2001-12-28;448~art2')
                ->required(),
        ];
    }

    /**
     * Get the tool's output schema.
     *
     * @return array<string, JsonSchema>
     */
    public function outputSchema(JsonSchema $schema) : array {
        $attoSchema = $schema->object([
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
        ]);

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
                'atto'  => $attoSchema->description('Dettaglio principale dell\'atto')->required(),
                'lista' => $schema->array()
                    ->items($attoSchema)
                    ->description('Elenco opzionale di risultati correlati o versioni dello stesso atto'),
                'message' => $schema->string()
                    ->nullable()
                    ->description('Messaggio informativo associato al contenuto data'),
            ])->description('Payload di risposta del dettaglio atto da URN')->required(),
        ];
    }
}
