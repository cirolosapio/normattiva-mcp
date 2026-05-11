<?php

declare(strict_types = 1);

namespace App\Mcp\Prompts;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

#[Description('Analizza un atto normativo e produce una scheda strutturata con obblighi, divieti, decorrenza e rinvii.')]
class AnalizzaAttoPrompt extends Prompt {
    /** Handle the prompt request. */
    public function handle(Request $request) : array {
        $validated = $request->validate([
            'testoAtto'        => ['required', 'string'],
            'titoloAtto'       => ['nullable', 'string'],
            'focus'            => ['nullable', 'string'],
            'livelloDettaglio' => ['nullable', 'string', 'in:sintetico,medio,approfondito'],
        ]);

        $livello = $validated['livelloDettaglio'] ?? 'medio';
        $focus = $validated['focus'] ?? 'quadro generale';
        $titolo = $validated['titoloAtto'] ?? 'Atto non specificato';

        $systemMessage = <<<PROMPT
            Sei un analista normativo specializzato in diritto italiano.

            Analizza il testo fornito con livello di dettaglio: {$livello}.
            Focus richiesto: {$focus}.

            Regole di output:
            1. Produci sezioni: Oggetto, Soggetti coinvolti, Obblighi, Divieti, Decorrenza/Vigenza, Rinvii normativi, Criticita interpretative.
            2. Riporta sempre citazioni puntuali (articolo/comma) quando presenti nel testo.
            3. Se il testo non contiene elementi sufficienti, indicarlo esplicitamente in "Dati mancanti".
            4. Non inventare riferimenti normativi non presenti nel testo.
            PROMPT;

        $userMessage = <<<PROMPT
            Analizza il seguente atto.

            Titolo: {$titolo}

            Testo atto:
            {$validated['testoAtto']}
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
                name: 'testoAtto',
                description: 'Testo dell atto da analizzare.',
                required: true,
            ),
            new Argument(
                name: 'titoloAtto',
                description: 'Titolo o riferimento dell atto (facoltativo).',
                required: false,
            ),
            new Argument(
                name: 'focus',
                description: 'Aspetto da approfondire (es. sanzioni, adempimenti PA, impatti imprese).',
                required: false,
            ),
            new Argument(
                name: 'livelloDettaglio',
                description: 'Livello di dettaglio: sintetico, medio, approfondito.',
                required: false,
            ),
        ];
    }
}
