<?php

declare(strict_types = 1);

namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\AppResource;
use Laravel\Mcp\Server\Attributes\AppMeta;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Ui\Enums\Library;

#[Description('Mostra i risultati della ricerca avanzata con filtri su denominazione, titolo, testo, date e vigenza.')]
#[AppMeta(libraries: [Library::Tailwind])]
class SearchActsAdvancedApp extends AppResource {
    public function handle(Request $request) : Response {
        return Response::view('mcp.search-acts-advanced-app', [
            'title' => $this->title(),
        ]);
    }
}
