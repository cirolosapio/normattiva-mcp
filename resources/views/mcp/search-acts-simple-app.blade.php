<x-mcp::app :title="$title">
  <x-slot:head>
    <script type="module">
      const el = {
        error: () => document.getElementById('error'),
        count: () => document.getElementById('result-count'),
        paging: () => document.getElementById('paging-info'),
        list: () => document.getElementById('acts-list'),
        empty: () => document.getElementById('empty-state'),
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
        const list = payload?.listaAtti;

        if (Array.isArray(list)) {
          return list;
        }

        return [];
      };

      const normalizeCount = (payload, acts) => {
        return Number(payload?.numeroAttiTrovati ?? acts.length);
      };

      const normalizePaging = (payload, acts) => {
        const currentPage = Number(payload?.paginaCorrente ?? 1);
        const totalPages = Number(payload?.numeroPagine ?? 1);
        const perPage = Number(payload?.numeroElementiPerPagina || acts.length);

        if (Number.isNaN(currentPage) || Number.isNaN(totalPages) || Number.isNaN(perPage)) {
          return null;
        }

        return {
          currentPage,
          totalPages,
          perPage,
        };
      };

      /**
       * Builds a Normattiva act detail URL if possible, otherwise returns null.
       * @param {object} [act]
       * @returns {string|null}
       */
      const buildNormattivaSearchUrl = (act) => {
        if (act && act.codiceRedazionale && act.dataGU) {
          const params = new URLSearchParams();
          params.set('atto.dataPubblicazioneGazzetta', act.dataGU);
          params.set('atto.codiceRedazionale', act.codiceRedazionale);
          if (act.articoloNumero !== undefined) params.set('atto.articolo.numero', act.articoloNumero);
          if (act.sottoArticolo !== undefined) params.set('atto.articolo.sottoArticolo', act.sottoArticolo);
          if (act.sottoArticolo1 !== undefined) params.set('atto.articolo.sottoArticolo1', act.sottoArticolo1);
          if (act.qId) params.set('qId', act.qId);
          if (act.tabID) params.set('tabID', act.tabID);
          if (act.title) params.set('title', act.title);
          return `https://www.normattiva.it/atto/caricaDettaglioAtto?${params.toString()}`;
        }
        return null;
      };

      const renderActs = (acts, app, searchText) => {
        const list = el.list();
        list.innerHTML = '';

        acts.forEach((act, index) => {
          const card = document.createElement('button');
          card.type = 'button';
          card.className =
            'w-full rounded-md border border-sky-300/60 dark:border-sky-400/50 bg-sky-500/5 dark:bg-sky-400/10 p-4 text-left transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400/70';

          const titleText = (act.titoloAtto || '').replace(/[\[\]\r\n]+/g, ' ').replace(/\s+/g, ' ').trim();
          const normalizedTitle = titleText.toLowerCase();

          const title = document.createElement('h3');
          title.className = 'text-sm font-semibold text-sky-800 dark:text-sky-200';
          title.textContent = titleText || `Atto ${index + 1}`;

          const rawSubtitle = (act.descrizioneAtto || '').replace(/\s+/g, ' ').trim();
          const normalizedSubtitle = rawSubtitle.toLowerCase();
          const subtitleText = normalizedSubtitle.length > 0
            && normalizedSubtitle !== normalizedTitle
            && !normalizedTitle.includes(normalizedSubtitle)
            ? rawSubtitle
            : '';
          const subtitle = document.createElement('p');
          subtitle.className = 'mt-1 text-xs text-sky-700/90 dark:text-sky-300/90';

          if (subtitleText.length > 0) {
            subtitle.textContent = subtitleText;
          }

          const meta = document.createElement('p');
          meta.className = 'mt-1 text-xs text-emerald-800 dark:text-emerald-200';
          const rawMetaText = [
            act.denominazioneAtto,
            act.dataGU ? `GU ${act.dataGU}` : null,
            act.numeroGU ? `n. ${act.numeroGU}` : null,
          ]
            .filter(Boolean)
            .join(' - ')
            .trim();
          const normalizedMeta = rawMetaText.toLowerCase();

          if (
            normalizedMeta.length > 0
            && normalizedMeta !== normalizedTitle
            && !normalizedTitle.includes(normalizedMeta)
          ) {
            meta.textContent = rawMetaText;
          }

          const extraInfo = [
            act.codiceRedazionale ? `Codice: ${act.codiceRedazionale}` : null,
            act.dataEmanazione ? `Emanazione: ${act.dataEmanazione.slice(0, 10)}` : null,
            act.dataUltimaModifica ? `Ultima modifica: ${act.dataUltimaModifica.slice(0, 10)}` : null,
          ].filter(Boolean);

          const extra = document.createElement('p');
          extra.className = 'mt-2 text-[11px] text-zinc-600 dark:text-zinc-300';

          if (extraInfo.length > 0) {
            extra.textContent = extraInfo.join(' | ');
          }

          const url = buildNormattivaSearchUrl(act);
          if (url) {
            card.className += ' cursor-pointer hover:border-emerald-400/70 hover:bg-emerald-400/10';
            card.addEventListener('click', async () => {
              await app.openLink(url);
            });
          }

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
          const searchText = params?.arguments?.testoRicerca ?? '';

          try {
            const result = await app.callServerTool('search-acts-simple-tool', params.arguments ?? {});
            const payload = parseToolTextResult(result);
            const acts = normalizeActs(payload);
            const totalCount = normalizeCount(payload, acts);
            const paging = normalizePaging(payload, acts);

            el.count().textContent = `${totalCount} atti trovati`;
            el.empty().classList.toggle('hidden', acts.length > 0);

            if (paging) {
              el.paging().textContent = `Pagina ${paging.currentPage} di ${paging.totalPages} - ${paging.perPage} elementi`;
            } else {
              el.paging().textContent = '';
            }

            renderActs(acts, app, searchText);
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

          <div class="flex items-center justify-between gap-4">
            <h2 class="text-base font-semibold tracking-tight text-sky-900 dark:text-sky-100">Risultati della ricerca semplice</h2>
            <span id="result-count" class="text-xs text-emerald-800 dark:text-emerald-200">0 atti trovati</span>
          </div>

          <p id="paging-info" class="-mt-3 text-xs text-zinc-600 dark:text-zinc-300"></p>

          <div id="acts-list" class="grid grid-cols-1 gap-3"></div>

          <div id="empty-state" class="rounded-md border border-dashed border-emerald-300/80 dark:border-emerald-400/70 bg-emerald-500/5 dark:bg-emerald-400/10 p-5 text-center text-sm text-emerald-800 dark:text-emerald-200">
            Nessun atto da mostrare.
          </div>
        </div>
      </section>
    </main>
  </div>
</x-mcp::app>
