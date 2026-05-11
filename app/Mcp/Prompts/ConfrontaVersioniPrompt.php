<?php

declare(strict_types = 1);

namespace App\Mcp\Prompts;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

#[Description('Confronta due versioni di un atto e riassume differenze sostanziali, impatti e aree di rischio.')]
class ConfrontaVersioniPrompt extends Prompt {
    /** Handle the prompt request. */
    public function handle(Request $request) : array {
        $validated = $request->validate([
            'versionePrecedente' => ['required', 'string'],
            'versioneCorrente'   => ['required', 'string'],
            'metadatiAtto'       => ['nullable', 'string'],
            'formatoOutput'      => ['nullable', 'string', 'in:bullet,tabella'],
        ]);

        $formato = $validated['formatoOutput'] ?? 'bullet';
        $metadati = $validated['metadatiAtto'] ?? 'Nessun metadato aggiuntivo.';

        $systemMessage = <<<PROMPT
            Sei un assistente legale che confronta versioni normative.

            Confronta versione precedente e versione corrente dell atto.

            Regole:
            1. Evidenzia solo differenze rilevanti dal punto di vista giuridico/operativo.
            2. Classifica le differenze in: aggiunte, abrogazioni, modifiche lessicali non sostanziali, modifiche sostanziali.
            3. Spiega impatto pratico su cittadini, imprese, PA quando desumibile.
            4. Formato output richiesto: {$formato}.
            PROMPT;

        $userMessage = <<<PROMPT
            Confronta queste due versioni dell atto.

            Metadati:
            {$metadati}

            Versione precedente:
            {$validated['versionePrecedente']}

            Versione corrente:
            {$validated['versioneCorrente']}
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
                name: 'versionePrecedente',
                description: 'Testo versione precedente dell atto.',
                required: true,
            ),
            new Argument(
                name: 'versioneCorrente',
                description: 'Testo versione corrente dell atto.',
                required: true,
            ),
            new Argument(
                name: 'metadatiAtto',
                description: 'Metadati facoltativi (URN, titolo, data, numero, fonte).',
                required: false,
            ),
            new Argument(
                name: 'formatoOutput',
                description: 'Formato risposta desiderato: bullet o tabella.',
                required: false,
            ),
        ];
    }
}
