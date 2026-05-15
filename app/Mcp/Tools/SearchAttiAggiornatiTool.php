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
#[Title('Recupera atti aggiornati')]
#[Description('Retrieve Normattiva acts updated within a date-time interval')]
class SearchAttiAggiornatiTool extends Tool {
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
}
