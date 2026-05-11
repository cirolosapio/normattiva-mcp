<?php

declare(strict_types = 1);

namespace App\Mcp\Prompts;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

#[Description('Spiega una legge in linguaggio chiaro, adattando tono e livello al pubblico destinatario.')]
class SpiegaLeggePrompt extends Prompt {
    /** Handle the prompt request. */
    public function handle(Request $request) : array {
        $validated = $request->validate([
            'testoAtto'      => ['required', 'string'],
            'pubblicoTarget' => ['nullable', 'string'],
            'tono'           => ['nullable', 'string'],
            'maxParole'      => ['nullable', 'integer', 'min:80', 'max:1200'],
        ]);

        $target = $validated['pubblicoTarget'] ?? 'cittadino non specialista';
        $tono = $validated['tono'] ?? 'chiaro e pratico';
        $maxParole = (string) ($validated['maxParole'] ?? 350);

        $systemMessage = <<<PROMPT
            Sei un assistente che traduce testi normativi in linguaggio semplice.

            Pubblico destinatario: {$target}.
            Tono: {$tono}.
            Lunghezza massima: {$maxParole} parole.

            Regole:
            1. Spiega cosa cambia in pratica, non solo il testo legale.
            2. Usa esempi concreti quando utili.
            3. Mantieni accuratezza giuridica e segnala limiti interpretativi.
            4. Chiudi con "Cosa fare adesso" in 3 punti operativi.
            PROMPT;

        $userMessage = <<<PROMPT
            Spiega in modo semplice il seguente testo normativo:

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
                description: 'Testo della norma da spiegare.',
                required: true,
            ),
            new Argument(
                name: 'pubblicoTarget',
                description: 'Destinatario della spiegazione (es. cittadino, PMI, ufficio HR).',
                required: false,
            ),
            new Argument(
                name: 'tono',
                description: 'Tono desiderato (es. divulgativo, tecnico-semplice, operativo).',
                required: false,
            ),
            new Argument(
                name: 'maxParole',
                description: 'Numero massimo indicativo di parole.',
                required: false,
            ),
        ];
    }
}
