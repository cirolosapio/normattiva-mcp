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
#[Title('Recupera tipologiche estensioni')]
#[Description('Elenca le estensioni/formati di file disponibili per i download di Normattiva')]
class GetAvailableExtensionsTool extends Tool {
    public function handle() : Response {
        return Response::json(
            Http::normattiva()
                ->get('tipologiche/estensioni')
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
                    'label' => $schema->string()->description('Codice estensione/formato')->required(),
                    'value' => $schema->string()->description('Descrizione estensione/formato')->required(),
                ]))
                ->description('Elenco delle estensioni/formati disponibili'),
        ];
    }
}
