<?php

declare(strict_types = 1);

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('List available act denominations and related codes from Normattiva lookup endpoint.')]
class GetTipologicheDenominazioneAttoTool extends Tool {
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
}
