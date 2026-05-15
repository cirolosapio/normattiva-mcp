<x-mcp::app :title="$title">
  <x-slot:head>
    <script>
      window.tailwind = window.tailwind || {};
      window.tailwind.config = {
        darkMode: 'class',
      };
    </script>
  </x-slot:head>

  <div
    class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-100 text-slate-950 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 dark:text-slate-50">
    <div class="mx-auto flex min-h-screen max-w-6xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
      <section
        class="rounded-3xl border border-slate-200/70 bg-white/85 p-6 shadow-[0_24px_80px_rgba(15,23,42,0.12)] backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-900/75">
        <div
          class="inline-flex w-fit items-center gap-2 rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-teal-700 dark:bg-teal-500/10 dark:text-teal-300">
          Normattiva MCP App
        </div>

        <div class="mt-4 space-y-3">
          <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-slate-50 sm:text-4xl" id="act-title">In attesa del dettaglio
            dell'atto</h1>
          <p class="max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-300 sm:text-base" id="act-subtitle">
            Quando il tool restituisce il payload, qui compare il testo HTML dell'articolo e puoi copiare l'URN o aprire la pagina pubblica su
            Normattiva.
          </p>
        </div>

        <div class="mt-5 flex flex-wrap gap-3">
          <button
            class="inline-flex items-center justify-center rounded-xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-600 disabled:cursor-not-allowed disabled:opacity-45"
            id="copy-urn-btn" type="button" disabled>Copia URN</button>
          <button
            class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-45 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800"
            id="open-normattiva-btn" type="button" disabled>Apri su Normattiva</button>
        </div>

        <div class="mt-4 flex flex-wrap gap-2" id="badge-row" hidden></div>
      </section>

      <div class="grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(320px,0.8fr)]">
        <section
          class="rounded-3xl border border-slate-200/70 bg-white/85 p-6 shadow-[0_24px_80px_rgba(15,23,42,0.12)] backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-900/75">
          <h2 class="mb-4 text-lg font-semibold text-slate-950 dark:text-slate-50">Articolo</h2>
          <div
            class="min-h-64 overflow-auto rounded-2xl border border-slate-200 bg-white p-5 text-slate-900 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100"
            id="article-html">
            <div
              class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-5 py-6 text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">
              Esegui il tool di dettaglio per visualizzare qui il contenuto HTML dell'atto.</div>
          </div>
        </section>

        <aside
          class="rounded-3xl border border-slate-200/70 bg-white/85 p-6 shadow-[0_24px_80px_rgba(15,23,42,0.12)] backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-900/75">
          <h3 class="mb-4 text-lg font-semibold text-slate-950 dark:text-slate-50">Metadati</h3>
          <div class="grid gap-3" id="meta-list">
            <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-950/60">
              <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">URN</div>
              <div class="mt-1 break-all text-sm font-medium text-slate-900 dark:text-slate-100" id="urn-value">Nessun URN ancora disponibile</div>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-950/60">
              <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Stato</div>
              <div class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100" id="status-value">In attesa di risposta dal server</div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>

  <script type="module">
    createMcpApp(async (app) => {
      const state = {
        urn: '',
        act: null,
      };

      const copyButton = document.getElementById('copy-urn-btn');
      const openButton = document.getElementById('open-normattiva-btn');
      const urnValue = document.getElementById('urn-value');
      const statusValue = document.getElementById('status-value');
      const titleValue = document.getElementById('act-title');
      const subtitleValue = document.getElementById('act-subtitle');
      const articleHtml = document.getElementById('article-html');
      const badgeRow = document.getElementById('badge-row');

      function buildNormattivaUrl(urn) {
        return urn ? `https://www.normattiva.it/uri-res/N2Ls?${encodeURI(urn)}` : 'https://www.normattiva.it/';
      }

      function setButtonsEnabled(enabled) {
        copyButton.disabled = !enabled;
        openButton.disabled = !enabled;
      }

      function renderBadges(act) {
        badgeRow.hidden = false;
        badgeRow.innerHTML = '';

        const labels = [];

        if (act.tipoProvvedimentoDescrizione) {
          labels.push(act.tipoProvvedimentoDescrizione);
        }

        if (act.tipoProvvedimentoCodice) {
          labels.push(`Codice ${act.tipoProvvedimentoCodice}`);
        }

        if (act.annoProvvedimento && act.numeroProvvedimento) {
          labels.push(`Provvedimento ${act.annoProvvedimento}/${act.numeroProvvedimento}`);
        }

        if (act.testoInVigore) {
          labels.push(act.testoInVigore);
        }

        labels.forEach((label) => {
          const badge = document.createElement('span');
          badge.className = 'badge';
          badge.textContent = label;
          badgeRow.appendChild(badge);
        });
      }

      function renderAct(payload) {
        const act = payload?.data?.atto ?? null;

        if (!act) {
          return;
        }

        state.act = act;
        state.urn = payload?.data?.urn ?? state.urn;

        titleValue.textContent = act.titolo ?? 'Dettaglio atto';
        subtitleValue.textContent = act.sottoTitolo ?? 'Dettaglio recuperato da Normattiva';
        urnValue.textContent = state.urn || 'URN non disponibile nel payload corrente';
        statusValue.textContent = payload?.message ?? payload?.data?.message ?? 'Risposta caricata correttamente';
        articleHtml.innerHTML = act.articoloHtml ?? '<div class="empty-state">Nessun contenuto HTML disponibile per questo articolo.</div>';

        renderBadges(act);
        setButtonsEnabled(Boolean(state.urn));
      }

      async function copyUrn() {
        if (!state.urn) {
          return;
        }

        await navigator.clipboard.writeText(state.urn);
        copyButton.textContent = 'URN copiata';

        setTimeout(() => {
          copyButton.textContent = 'Copia URN';
        }, 1200);
      }

      copyButton.addEventListener('click', copyUrn);
      openButton.addEventListener('click', async () => {
        if (!state.urn) {
          return;
        }

        await app.openLink(buildNormattivaUrl(state.urn));
      });

      app.onToolInput((params) => {
        try {
          const input = JSON.parse(params.arguments ?? '{}');

          if (input.urn) {
            state.urn = input.urn;
            urnValue.textContent = state.urn;
            setButtonsEnabled(true);
          }
        } catch {
          // Ignore malformed input state.
        }
      });

      app.onToolResult((params) => {
        try {
          const payload = JSON.parse(params.result?.content?.[0]?.text ?? '{}');
          renderAct(payload);
        } catch {
          statusValue.textContent = 'Impossibile interpretare la risposta del server';
        }
      });
    });
  </script>
</x-mcp::app>
