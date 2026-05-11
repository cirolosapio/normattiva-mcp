<?php

declare(strict_types = 1);

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Get act detail from Normattiva using a NIR URN, for example urn:nir:stato:legge:2001-12-28;448~art2.')]
class GetDettaglioAttoUrnTool extends Tool {
    /** Handle the tool request. */
    public function handle(Request $request) : Response {
        $validated = $request->validate([
            'urn' => ['required', 'string', 'starts_with:urn:'],
        ]);

        return Http::normattiva()
            ->post('atto/dettaglio-atto-urn', $validated)
            ->throw();
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema) : array {
        return [
            'urn' => $schema->string()
                ->description('NIR URN identifying the legal act or a specific article.')
                ->required(),
        ];
    }
}
