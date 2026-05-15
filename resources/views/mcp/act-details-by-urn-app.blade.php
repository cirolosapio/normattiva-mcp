<x-mcp::app :title="$title">
  <x-slot:head>
    <script>
      window.tailwind = window.tailwind || {};
      window.tailwind.config = {
        darkMode: 'class',
      };
    </script>
    <style>
      .act-html-body {
        display: none;
      }

      .act-html-body.open {
        display: block;
      }

      .chevron {
        transition: transform 0.2s ease;
      }

      .chevron.open {
        transform: rotate(180deg);
      }
    </style>
  </x-slot:head>

  <div
    class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-100 text-slate-950 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 dark:text-slate-50">
    <div class="mx-auto flex min-h-screen max-w-4xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">

      <section
        class="rounded-3xl border border-slate-200/70 bg-white/85 p-6 shadow-[0_24px_80px_rgba(15,23,42,0.12)] backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-900/75">
        <div
          class="inline-flex w-fit items-center gap-2 rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-teal-700 dark:bg-teal-500/10 dark:text-teal-300">
          Normattiva MCP App
        </div>

        <div class="mt-4 space-y-3">
          <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-slate-50 sm:text-4xl" id="page-title">
            In attesa degli atti
          </h1>
          <p class="max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-300 sm:text-base" id="page-subtitle">
            Quando il tool restituisce il payload, qui compare la lista degli atti trovati per l'URN fornito.
          </p>
        </div>

        <div class="mt-5 flex flex-wrap gap-3">
          <button
            class="inline-flex items-center justify-center rounded-xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-600 disabled:cursor-not-allowed disabled:opacity-45"
            id="run-btn" type="button" disabled>Esegui</button>
          <button
            class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-45 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800"
            id="open-normattiva-btn" type="button" disabled>Apri su Normattiva</button>
        </div>

        <p class="mt-4 text-xs text-slate-500 dark:text-slate-400" id="status-line">In attesa di risposta dal server</p>
      </section>

      <section id="acts-list" class="flex flex-col gap-4"></section>

    </div>
  </div>

  <script type="module">
    createMcpApp(async (app) => {
      const state = {
        urn: '',
        lastToolCall: null,
      };

      const runBtn = document.getElementById('run-btn');
      const openBtn = document.getElementById('open-normattiva-btn');
      const pageTitle = document.getElementById('page-title');
      const pageSubtitle = document.getElementById('page-subtitle');
      const statusLine = document.getElementById('status-line');
      const actsList = document.getElementById('acts-list');

      function buildNormattivaUrl(urn) {
        return urn ? `https://www.normattiva.it/uri-res/N2Ls?${encodeURI(urn)}` : 'https://www.normattiva.it/';
      }

      function buildBadgesHtml(act) {
        const labels = [];

        if (act.tipoProvvedimentoDescrizione) {
          labels.push(act.tipoProvvedimentoDescrizione);
        }

        if (act.tipoProvvedimentoCodice) {
          labels.push(`Codice ${act.tipoProvvedimentoCodice}`);
        }

        if (act.annoProvvedimento && act.numeroProvvedimento) {
          labels.push(`${act.annoProvvedimento}/${act.numeroProvvedimento}`);
        }

        if (act.testoInVigore) {
          labels.push(act.testoInVigore);
        }

        if (act.dataPubblicazioneInGazzetta) {
          labels.push(`GU ${act.dataPubblicazioneInGazzetta}`);
        }

        return labels
          .map(
            (l) =>
            `<span class="inline-flex items-center rounded-full bg-teal-50 px-2.5 py-0.5 text-xs font-medium text-teal-700 dark:bg-teal-500/10 dark:text-teal-300">${l}</span>`,
          )
          .join('');
      }

      function createActCard(act, index) {
        const bodyId = `act-body-${index}`;
        const chevronId = `act-chevron-${index}`;

        const card = document.createElement('div');
        card.className =
          'rounded-3xl border border-slate-200/70 bg-white/85 shadow-[0_24px_80px_rgba(15,23,42,0.12)] backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-900/75 overflow-hidden';

        const hasHtml = Boolean(act.articoloHtml);

        card.innerHTML = `
          <button
            class="w-full flex items-start justify-between gap-4 p-5 text-left transition ${hasHtml ? 'hover:bg-slate-50/60 dark:hover:bg-slate-800/40 cursor-pointer' : 'cursor-default'}"
            data-body="${bodyId}"
            data-chevron="${chevronId}"
            ${!hasHtml ? 'disabled' : ''}
          >
            <div class="flex-1 min-w-0">
              <div class="flex flex-wrap gap-1.5 mb-2">${buildBadgesHtml(act)}</div>
              <h3 class="font-semibold text-slate-900 dark:text-slate-50 leading-snug">${act.titolo ?? 'Atto senza titolo'}</h3>
              ${act.sottoTitolo ? `<p class="mt-1 text-sm text-slate-500 dark:text-slate-400 line-clamp-2">${act.sottoTitolo}</p>` : ''}
              ${
                act.articoloDataInizioVigenza || act.articoloDataFineVigenza
                  ? `<p class="mt-1.5 text-xs text-slate-400 dark:text-slate-500">Vigenza: ${act.articoloDataInizioVigenza ?? '?'} → ${act.articoloDataFineVigenza ?? 'in corso'}</p>`
                  : ''
              }
            </div>
            ${
              hasHtml
                ? `<svg id="${chevronId}" class="chevron mt-0.5 shrink-0 w-5 h-5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>`
                : `<span class="mt-0.5 shrink-0 text-xs text-slate-400 dark:text-slate-500">Nessun HTML</span>`
            }
          </button>
          <div id="${bodyId}" class="act-html-body border-t border-slate-100 dark:border-slate-800 px-5 py-5">
            <div class="overflow-auto rounded-2xl border border-slate-200 bg-white p-5 text-slate-900 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100 text-sm leading-relaxed">
              ${act.articoloHtml ?? '<em class="text-slate-400">Nessun contenuto HTML disponibile.</em>'}
            </div>
          </div>
        `;

        const toggleBtn = card.querySelector('button[data-body]');

        if (hasHtml && toggleBtn) {
          toggleBtn.addEventListener('click', () => {
            const body = document.getElementById(bodyId);
            const chevron = document.getElementById(chevronId);

            if (body) {
              body.classList.toggle('open');
            }

            if (chevron) {
              chevron.classList.toggle('open');
            }
          });
        }

        return card;
      }

      function renderList(payload) {
        const mainAct = payload?.data?.atto ?? null;
        const lista = Array.isArray(payload?.data?.lista) ? payload.data.lista : [];

        state.urn = payload?.urn ?? payload?.data?.urn ?? state.urn;

        const allActs = [];

        if (mainAct) {
          allActs.push(mainAct);
        }

        lista.forEach((item) => allActs.push(item));

        if (allActs.length === 0) {
          actsList.innerHTML =
            '<div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-5 py-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">Nessun atto trovato nel payload.</div>';

          return;
        }

        pageTitle.textContent = allActs.length === 1 ? allActs[0].titolo ?? 'Dettaglio atto' : `${allActs.length} atti trovati`;
        pageSubtitle.textContent = state.urn ?? '';
        statusLine.textContent = payload?.message ?? payload?.data?.message ?? 'Risposta caricata correttamente';
        openBtn.disabled = !state.urn;

        actsList.innerHTML = '';
        allActs.forEach((act, index) => {
          actsList.appendChild(createActCard(act, index));
        });

        // Auto-expand first card if it has HTML
        if (allActs[0]?.articoloHtml) {
          const firstBody = document.getElementById('act-body-0');
          const firstChevron = document.getElementById('act-chevron-0');

          if (firstBody) {
            firstBody.classList.add('open');
          }

          if (firstChevron) {
            firstChevron.classList.add('open');
          }
        }
      }

      async function runTool() {
        if (!state.lastToolCall) {
          return;
        }

        runBtn.disabled = true;
        runBtn.textContent = 'Caricamento…';

        try {
          const result = await app.callServerTool({
            name: state.lastToolCall.name,
            arguments: JSON.parse(state.lastToolCall.arguments ?? '{}'),
          });

          if (result.isError) {
            statusLine.textContent = 'Il tool ha restituito un errore';

            return;
          }

          const payload = JSON.parse(result.content?.[0]?.text ?? '{}');
          renderList(payload);
        } catch {
          statusLine.textContent = 'Impossibile eseguire il tool';
        } finally {
          runBtn.disabled = false;
          runBtn.textContent = 'Esegui';
        }
      }

      runBtn.addEventListener('click', runTool);
      openBtn.addEventListener('click', async () => {
        if (!state.urn) {
          return;
        }

        await app.openLink(buildNormattivaUrl(state.urn));
      });

      app.onToolInput((params) => {
        state.lastToolCall = {
          name: params.name,
          arguments: params.arguments
        };

        try {
          const input = JSON.parse(params.arguments ?? '{}');

          if (input.urn) {
            state.urn = input.urn;
          }
        } catch {
          // Ignore malformed input state.
        }

        runBtn.disabled = false;
      });

      app.onToolResult((params) => {
        try {
          const payload = JSON.parse(params.result?.content?.[0]?.text ?? '{}');
          renderList(payload);
        } catch {
          statusLine.textContent = 'Impossibile interpretare la risposta del server';
        }
      });
    });
  </script>
</x-mcp::app>
