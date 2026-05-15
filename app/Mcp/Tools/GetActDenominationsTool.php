<?php

declare(strict_types = 1);

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[IsDestructive(false)]
#[IsIdempotent(false)]
#[Title('Recupera tipologiche denominazione atto')]
#[Description('Elenca le denominazioni di atto disponibili e i codici correlati dall\'endpoint di lookup di Normattiva')]
class GetActDenominationsTool extends Tool {
    public function handle() : Response {
        return Response::json(
            Http::normattiva()
                ->get('tipologiche/denominazione-atto')
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
        return [];
    }

    /**
     * Get the tool's output schema.
     *
     * @return array<string, JsonSchema>
     */
    public function outputSchema(JsonSchema $schema) : array {
        return [
            'items' => $schema->array()
                ->items($schema->object([
                    'label' => $schema->string()->description('Codice della denominazione atto')->required(),
                    'value' => $schema->string()->description('Descrizione della denominazione atto')->required(),
                ]))
                ->description('Elenco delle denominazioni di atto disponibili'),
        ];
    }
}
