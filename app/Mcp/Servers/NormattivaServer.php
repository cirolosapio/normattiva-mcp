<?php

declare(strict_types = 1);

namespace App\Mcp\Servers;

use App\Mcp\Prompts\AnalizzaAttoPrompt;
use App\Mcp\Prompts\ConfrontaVersioniPrompt;
use App\Mcp\Prompts\SpiegaLeggePrompt;
use App\Mcp\Prompts\VerificaVigenzaPrompt;
use App\Mcp\Tools\GetDettaglioAttoTool;
use App\Mcp\Tools\GetDettaglioAttoUrnTool;
use App\Mcp\Tools\GetTipologicheClasseProvvedimentoTool;
use App\Mcp\Tools\GetTipologicheDenominazioneAttoTool;
use App\Mcp\Tools\GetTipologicheEstensioniTool;
use App\Mcp\Tools\SearchAttiAggiornatiTool;
use App\Mcp\Tools\SearchAttiAvanzataTool;
use App\Mcp\Tools\SearchAttiSempliceTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Normattiva Server')]
#[Version('0.0.1')]
#[Instructions('Use this server to search Normattiva acts, inspect act details, and fetch legal lookup metadata.')]
class NormattivaServer extends Server {
    protected array $tools = [
        SearchAttiSempliceTool::class,
        SearchAttiAvanzataTool::class,
        SearchAttiAggiornatiTool::class,
        GetDettaglioAttoUrnTool::class,
        GetDettaglioAttoTool::class,
        GetTipologicheDenominazioneAttoTool::class,
        GetTipologicheClasseProvvedimentoTool::class,
        GetTipologicheEstensioniTool::class,
    ];
    protected array $resources = [
        //
    ];
    protected array $prompts = [
        AnalizzaAttoPrompt::class,
        ConfrontaVersioniPrompt::class,
        SpiegaLeggePrompt::class,
        VerificaVigenzaPrompt::class,
    ];
}
