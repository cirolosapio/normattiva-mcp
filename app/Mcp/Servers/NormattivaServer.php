<?php

declare(strict_types = 1);

namespace App\Mcp\Servers;

use App\Mcp\Prompts\CheckLawValidityPrompt;
use App\Mcp\Prompts\CompareRegulatoryVersionsPrompt;
use App\Mcp\Prompts\ExplainLawPrompt;
use App\Mcp\Prompts\NormativeActAnalysisPrompt;
use App\Mcp\Tools\GetActDenominationsTool;
use App\Mcp\Tools\GetActDetailsByUrnTool;
use App\Mcp\Tools\GetActDetailsTool;
use App\Mcp\Tools\GetAvailableExtensionsTool;
use App\Mcp\Tools\GetProvisionClassTypesTool;
use App\Mcp\Tools\SearchActsAdvancedTool;
use App\Mcp\Tools\SearchActsSimpleTool;
use App\Mcp\Tools\SearchUpdatedActsTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Normattiva Server')]
#[Version('0.0.7')]
#[Instructions('Utilizza questo server per cercare atti normativi su Normattiva, ispezionare i dettagli degli atti e recuperare i metadati di lookup legale')]
class NormattivaServer extends Server {
    protected array $tools = [
        SearchActsSimpleTool::class,
        SearchActsAdvancedTool::class,
        SearchUpdatedActsTool::class,
        GetActDetailsByUrnTool::class,
        GetActDetailsTool::class,
        GetActDenominationsTool::class,
        GetProvisionClassTypesTool::class,
        GetAvailableExtensionsTool::class,
    ];
    protected array $resources = [
        //
    ];
    protected array $prompts = [
        NormativeActAnalysisPrompt::class,
        CompareRegulatoryVersionsPrompt::class,
        ExplainLawPrompt::class,
        CheckLawValidityPrompt::class,
    ];
}
