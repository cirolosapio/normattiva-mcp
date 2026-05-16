<?php

declare(strict_types = 1);

namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\AppResource;
use Laravel\Mcp\Server\Attributes\AppMeta;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Ui\Enums\Library;

#[Description('Mostra gli atti aggiornati in un intervallo temporale con elenco e metadati principali.')]
#[AppMeta(libraries: [Library::Tailwind])]
class SearchUpdatedActsApp extends AppResource {
    public function handle(Request $request) : Response {
        return Response::view('mcp.search-updated-acts-app', [
            'title' => $this->title(),
        ]);
    }
}
