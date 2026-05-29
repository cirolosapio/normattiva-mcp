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

#[Description('Mostra il dettaglio di un atto con HTML renderizzato e azioni rapide per URN e Normattiva.')]
#[AppMeta(
    permissions: [Permission::ClipboardWrite],
    libraries: [Library::Tailwind],
    domain: '310ccb64d682b82507cb808fc51cedbf.claudemcpcontent.com'
)]
class ActDetailsApp extends AppResource {
    public function handle(Request $request) : Response {
        return Response::view('mcp.act-details-app', [
            'title' => $this->title(),
        ]);
    }
}
