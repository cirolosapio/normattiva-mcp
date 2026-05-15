<?php

declare(strict_types = 1);

namespace App\Mcp\Tools;

use App\Mcp\Resources\TestApp;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\RendersApp;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[Name('test')]
#[IsDestructive(false)]
#[IsIdempotent(true)]
#[Description('Tool di test con view dedicata e azione Run.')]
#[RendersApp(resource: TestApp::class)]
class TestTool extends Tool {
    public function handle() : Response {
        return Response::json([
            'status'  => 'ok',
            'message' => 'Run eseguito correttamente',
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
