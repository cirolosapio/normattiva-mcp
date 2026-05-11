<?php

declare(strict_types = 1);

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('List available provision classes and related codes from Normattiva lookup endpoint.')]
class GetTipologicheClasseProvvedimentoTool extends Tool {
    public function handle() {
        return Http::normattiva()
            ->get('tipologiche/classe-provvedimento')
            ->throw()
            ->json();
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
