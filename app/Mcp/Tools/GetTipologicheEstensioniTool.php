<?php

declare(strict_types = 1);

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('List available file extensions/formats for Normattiva downloads.')]
class GetTipologicheEstensioniTool extends Tool {
    /** Handle the tool request. */
    public function handle(Request $request) : Response {
        $response = Http::normattiva()->get('/tipologiche/estensioni');

        return Response::json([
            'status' => $response->status(),
            'data'   => $response->json(),
        ]);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema) : array {
        return [];
    }
}
