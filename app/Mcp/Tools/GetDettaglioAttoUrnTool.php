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
class GetDettaglioAttoUrnTool extends Tool {
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
}
