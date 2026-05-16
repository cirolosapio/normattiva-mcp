<x-mcp::app :title="$title">
  <x-slot:head>
    <script type="module">
      const el = {
        error: () => document.getElementById('error'),
        count: () => document.getElementById('result-count'),
        list: () => document.getElementById('acts-list'),
        empty: () => document.getElementById('empty-state'),
      };

      const buildNormattivaUrl = (urn) => {
        return urn ? `https://www.normattiva.it/uri-res/N2Ls?${encodeURI(urn)}` : 'https://www.normattiva.it/';
      };

      const parseToolTextResult = (result) => {
        const raw = result?.content?.[0]?.text;

        if (!raw) {
          return null;
        }

        try {
          return JSON.parse(raw);
        } catch {
          return null;
        }
      };

      const normalizeActs = (payload) => {
        const lista = payload?.data?.lista;

        if (Array.isArray(lista) && lista.length > 0) {
          return lista;
        }

        const atto = payload?.data?.atto;

        if (atto && typeof atto === 'object') {
          return [atto];
        }

        return [];
      };

      const renderActs = (acts, app, searchUrn) => {
        const list = el.list();
        list.innerHTML = '';

        acts.forEach((act, index) => {
          const card = document.createElement('button');
          card.type = 'button';
          card.className =
            'w-full rounded-md border border-sky-300/60 dark:border-sky-400/50 bg-sky-500/5 dark:bg-sky-400/10 p-4 text-left transition hover:border-emerald-400/70 hover:bg-emerald-400/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400/70';

          const title = document.createElement('h3');
          title.className = 'text-sm font-semibold text-sky-800 dark:text-sky-200';
          title.textContent = act.titolo || `Atto ${index + 1}`;

          const subtitleText = (act.sottoTitolo || '').trim();
          const subtitle = document.createElement('p');
          subtitle.className = 'mt-1 text-xs text-sky-700/90 dark:text-sky-300/90';

          if (subtitleText.length > 0) {
            subtitle.textContent = subtitleText;
          }

          const meta = document.createElement('p');
          meta.className = 'mt-1 text-xs text-emerald-800 dark:text-emerald-200';
          meta.textContent = [
              act.tipoProvvedimentoDescrizione,
              act.annoProvvedimento,
              act.numeroProvvedimento ? `n. ${act.numeroProvvedimento}` : null,
            ]
            .filter(Boolean)
            .join(' - ');

          const extraInfo = [
            act.dataPubblicazioneInGazzetta ? `GU: ${act.dataPubblicazioneInGazzetta}` : null,
            act.articoloDataInizioVigenza ? `Inizio vigenza: ${act.articoloDataInizioVigenza}` : null,
            act.articoloDataFineVigenza ? `Fine vigenza: ${act.articoloDataFineVigenza}` : null,
          ].filter(Boolean);

          const extra = document.createElement('p');
          extra.className = 'mt-2 text-[11px] text-zinc-600 dark:text-zinc-300';

          if (extraInfo.length > 0) {
            extra.textContent = extraInfo.join(' | ');
          }

          const validity = document.createElement('p');
          validity.className = 'mt-3 text-xs text-zinc-700 dark:text-zinc-200';
          validity.textContent = act.testoInVigore || 'Stato vigenza non disponibile';

          const urnToOpen = act.urn || searchUrn || null;
          const openHint = document.createElement('p');
          openHint.className = 'mt-3 text-[11px] font-medium text-emerald-700 dark:text-emerald-300';
          openHint.textContent = urnToOpen
            ? 'Apri su Normattiva'
            : 'Link Normattiva non disponibile per questo risultato';

          card.addEventListener('click', async () => {
            if (!urnToOpen) {
              return;
            }

            await app.openLink(buildNormattivaUrl(urnToOpen));
          });

          card.append(title);

          if (subtitleText.length > 0) {
            card.append(subtitle);
          }

          if (meta.textContent.length > 0) {
            card.append(meta);
          }

          if (extraInfo.length > 0) {
            card.append(extra);
          }

          card.append(validity, openHint);
          list.appendChild(card);
        });
      };

      const setError = (error) => {
        const errorNode = el.error();
        errorNode.textContent = `Errore: ${error?.message || JSON.stringify(error)}`;
        errorNode.classList.remove('hidden');
      };

      const clearError = () => {
        const errorNode = el.error();
        errorNode.textContent = '';
        errorNode.classList.add('hidden');
      };

      createMcpApp(async (app) => {
        app.onToolInput(async (params) => {
          clearError();
          const searchUrn = params?.arguments?.urn ?? null;

          try {
            const result = await app.callServerTool('get-act-details-by-urn-tool', params.arguments ?? {});
            const payload = parseToolTextResult(result);
            const acts = normalizeActs(payload);

            el.count().textContent = `${acts.length} atti trovati`;
            el.empty().classList.toggle('hidden', acts.length > 0);

            renderActs(acts, app, searchUrn);
          } catch (error) {
            console.error('Error calling tool:', error);
            setError(error);
          }
        });
      });
    </script>
  </x-slot:head>

  <div class="min-h-screen px-4 py-8">
    <main class="relative mx-auto w-full max-w-3xl">
      <section class="rounded-lg border border-sky-300/60 dark:border-sky-400/50 bg-sky-500/5 dark:bg-sky-400/10 p-6">
        <div class="grid gap-5">
          <div id="error" class="hidden rounded-md border border-rose-400/60 bg-rose-500/10 px-3 py-2 text-sm text-rose-800 dark:text-rose-200"></div>

          <div class="flex items-center justify-between">
            <h2 class="text-base font-semibold tracking-tight text-sky-900 dark:text-sky-100">Risultati della ricerca</h2>
            <span id="result-count" class="text-xs text-emerald-800 dark:text-emerald-200">0 atti trovati</span>
          </div>

          <p class="-mt-3 text-xs text-zinc-600 dark:text-zinc-300">Seleziona un risultato per aprire la legge su Normattiva.</p>

          <div id="acts-list" class="grid grid-cols-1 gap-3"></div>

          <div id="empty-state" class="rounded-md border border-dashed border-emerald-300/80 dark:border-emerald-400/70 bg-emerald-500/5 dark:bg-emerald-400/10 p-5 text-center text-sm text-emerald-800 dark:text-emerald-200">
            Nessun risultato da mostrare.
          </div>
        </div>
      </section>
    </main>
  </div>
</x-mcp::app>
