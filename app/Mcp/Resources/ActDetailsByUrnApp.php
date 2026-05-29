<?php

declare(strict_types = 1);

namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\AppResource;
use Laravel\Mcp\Server\Attributes\AppMeta;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Ui\Enums\Library;
use Laravel\Mcp\Server\Ui\Enums\Permission;

#[Description('Mostra il dettaglio di un atto cercato per URN con azione rapida per aprire su Normattiva.')]
#[AppMeta(
    connectDomains: ['https://api.normattiva.it'],
    permissions: [Permission::ClipboardWrite],
    libraries: [Library::Tailwind],
    domain: '310ccb64d682b82507cb808fc51cedbf.claudemcpcontent.com'
)]
class ActDetailsByUrnApp extends AppResource {
    public function handle(Request $request) : Response {
        return Response::view('mcp.act-details-by-urn-app', [
            'title' => $this->title(),
        ]);
    }
}
