<?php

declare(strict_types = 1);

namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\AppResource;
use Laravel\Mcp\Server\Attributes\AppMeta;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Ui\Enums\Library;

#[Description('Mostra i risultati della ricerca semplice con elenco atti e metadati principali.')]
#[AppMeta(
    libraries: [Library::Tailwind],
    domain: '310ccb64d682b82507cb808fc51cedbf.claudemcpcontent.com',
    prefersBorder: false,
)]
class SearchActsSimpleApp extends AppResource {
    public function handle(Request $request) : Response {
        return Response::view('mcp.search-acts-simple-app', [
            'title' => $this->title(),
        ]);
    }
}
