<?php

declare(strict_types = 1);

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Get act detail using redaction parameters such as codiceRedazionale, data GU, article and validity date.')]
class GetDettaglioAttoTool extends Tool {
    /** Handle the tool request. */
    public function handle(Request $request) : Response {
        $validated = $request->validate([
            'dataGU'            => ['required', 'date_format:Y-m-d'],
            'codiceRedazionale' => ['required', 'string', 'max:32'],
            'idArticolo'        => ['nullable', 'integer', 'min:0'],
            'sottoArticolo'     => ['nullable', 'integer', 'min:0'],
            'sottoArticolo1'    => ['nullable', 'integer', 'min:0'],
            'dataVigenza'       => ['nullable', 'date_format:Y-m-d'],
            'idGruppo'          => ['nullable', 'integer', 'min:0'],
            'progressivo'       => ['nullable', 'integer', 'min:0'],
            'versione'          => ['nullable', 'integer', 'min:0'],
        ]);

        return Http::normattiva()
            ->post('atto/dettaglio-atto', $validated)
            ->throw()
            ->json();
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema) : array {
        return [
            'dataGU' => $schema->string()
                ->description('Official Gazette date in format Y-m-d.')
                ->required(),
            'codiceRedazionale' => $schema->string()
                ->description('Normattiva redaction code, for example 088G0458.')
                ->required(),
            'idArticolo'     => $schema->integer()->description('Optional article identifier.'),
            'sottoArticolo'  => $schema->integer()->description('Optional sub-article identifier.'),
            'sottoArticolo1' => $schema->integer()->description('Optional nested sub-article identifier.'),
            'dataVigenza'    => $schema->string()->description('Optional validity date in format Y-m-d.'),
            'idGruppo'       => $schema->integer()->description('Optional group identifier.'),
            'progressivo'    => $schema->integer()->description('Optional progressive identifier.'),
            'versione'       => $schema->integer()->description('Optional version number.'),
        ];
    }
}
