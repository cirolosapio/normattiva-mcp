<?php

declare(strict_types = 1);

namespace App\Mcp\Prompts;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

#[Description('Verifica la vigenza di un atto a una data specifica e indica modifiche o abrogazioni rilevanti.')]
class VerificaVigenzaPrompt extends Prompt {
    /** Handle the prompt request. */
    public function handle(Request $request) : array {
        $validated = $request->validate([
            'riferimentoAtto' => ['required', 'string'],
            'dataRiferimento' => ['nullable', 'date_format:Y-m-d'],
            'contestoDomanda' => ['nullable', 'string'],
        ]);

        $dataRiferimento = $validated['dataRiferimento'] ?? now()->toDateString();
        $contesto = $validated['contestoDomanda'] ?? 'Valutazione generale della vigenza.';

        $systemMessage = <<<PROMPT
            Sei un assistente esperto nel controllo di vigenza normativa.

            Obiettivo: verificare lo stato dell atto alla data {$dataRiferimento}.

            Regole operative:
            1. Indica chiaramente se l atto risulta vigente, parzialmente vigente, abrogato o non determinabile con i dati disponibili.
            2. Se presenti, segnala modifiche e abrogazioni successive con riferimento puntuale.
            3. Se il riferimento e ambiguo, chiedi i chiarimenti minimi necessari.
            4. Non formulare consulenza legale definitiva: limita la risposta a un analisi documentale.
            PROMPT;

        $userMessage = <<<PROMPT
            Verifica la vigenza del seguente atto.

            Riferimento atto:
            {$validated['riferimentoAtto']}

            Contesto domanda:
            {$contesto}
            PROMPT;

        return [
            Response::text($systemMessage)->asAssistant(),
            Response::text($userMessage),
        ];
    }

    /**
     * Get the prompt's arguments.
     *
     * @return array<int, Argument>
     */
    public function arguments() : array {
        return [
            new Argument(
                name: 'riferimentoAtto',
                description: 'Riferimento atto (es. URN, legge 123/2020, codice redazionale).',
                required: true,
            ),
            new Argument(
                name: 'dataRiferimento',
                description: 'Data della verifica in formato Y-m-d. Se omessa usa oggi.',
                required: false,
            ),
            new Argument(
                name: 'contestoDomanda',
                description: 'Contesto applicativo della verifica (es. appalto pubblico, lavoro, fisco).',
                required: false,
            ),
        ];
    }
}
