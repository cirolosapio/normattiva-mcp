<?php

declare(strict_types = 1);

namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\AppResource;
use Laravel\Mcp\Server\Attributes\AppMeta;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('App MCP minimale per verificare che il pulsante Run chiami il tool test e mostri il risultato.')]
#[AppMeta]
class TestApp extends AppResource {
    public function handle(Request $request) : Response {
        return Response::view('mcp.test-app', [
            'title' => $this->title(),
        ]);
    }
}
