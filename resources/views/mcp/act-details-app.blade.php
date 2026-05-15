<x-mcp::app :title="$title">
  <x-slot:head>
    <style>
      :root {
        --panel-bg: color-mix(in srgb, var(--color-background-primary) 86%, transparent);
        --panel-border: color-mix(in srgb, var(--color-border-primary) 75%, transparent);
        --accent: #0f766e;
        --accent-weak: color-mix(in srgb, var(--accent) 14%, transparent);
      }

      body {
        margin: 0;
        background:
          radial-gradient(circle at top left, color-mix(in srgb, var(--accent) 12%, transparent), transparent 38%),
          radial-gradient(circle at top right, color-mix(in srgb, #1d4ed8 10%, transparent), transparent 28%),
          var(--color-background-primary);
        color: var(--color-text-primary);
        font-family: var(--font-sans);
      }

      .shell {
        max-width: 1100px;
        margin: 0 auto;
        padding: 24px;
      }

      .hero,
      .panel {
        border: 1px solid var(--panel-border);
        background: var(--panel-bg);
        backdrop-filter: blur(16px);
        border-radius: 24px;
        box-shadow: 0 24px 80px rgba(15, 23, 42, 0.12);
      }

      .hero {
        padding: 24px;
        display: grid;
        gap: 16px;
      }

      .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: fit-content;
        padding: 6px 12px;
        border-radius: 999px;
        background: var(--accent-weak);
        color: var(--accent);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
      }

      .title {
        margin: 0;
        font-size: clamp(28px, 4vw, 48px);
        line-height: 1.02;
        letter-spacing: -0.04em;
      }

      .subtitle {
        margin: 0;
        color: var(--color-text-secondary);
        font-size: 16px;
        line-height: 1.6;
        max-width: 70ch;
      }

      .actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
      }

      .button {
        appearance: none;
        border: 0;
        border-radius: 14px;
        padding: 12px 16px;
        font: inherit;
        font-weight: 700;
        cursor: pointer;
        transition: transform 140ms ease, opacity 140ms ease, background-color 140ms ease;
      }

      .button:hover {
        transform: translateY(-1px);
      }

      .button-primary {
        background: var(--accent);
        color: white;
      }

      .button-secondary {
        background: color-mix(in srgb, var(--color-border-primary) 18%, transparent);
        color: var(--color-text-primary);
        border: 1px solid var(--panel-border);
      }

      .button:disabled {
        opacity: 0.45;
        cursor: not-allowed;
        transform: none;
      }

      .grid {
        display: grid;
        gap: 18px;
        margin-top: 18px;
      }

      @media (min-width: 960px) {
        .grid {
          grid-template-columns: minmax(0, 1.2fr) minmax(320px, 0.8fr);
        }
      }

      .panel {
        padding: 20px;
      }

      .panel h2,
      .panel h3 {
        margin: 0 0 12px;
        font-size: 18px;
      }

      .meta-list {
        display: grid;
        gap: 10px;
      }

      .meta-row {
        display: grid;
        gap: 4px;
        padding: 12px;
        border-radius: 16px;
        background: color-mix(in srgb, var(--color-border-primary) 10%, transparent);
      }

      .meta-label {
        color: var(--color-text-secondary);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
      }

      .meta-value {
        font-weight: 600;
        line-height: 1.5;
      }

      .badge-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
      }

      .badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        background: color-mix(in srgb, var(--color-border-primary) 14%, transparent);
        color: var(--color-text-primary);
      }

      .article {
        padding: 22px;
        border-radius: 20px;
        background: white;
        color: #111827;
        overflow: auto;
        min-height: 200px;
        line-height: 1.7;
      }

      .article :where(h1, h2, h3, h4, h5, h6) {
        line-height: 1.2;
      }

      .empty-state {
        color: var(--color-text-secondary);
        border: 1px dashed var(--panel-border);
        border-radius: 18px;
        padding: 22px;
        background: color-mix(in srgb, var(--color-border-primary) 8%, transparent);
      }
    </style>
  </x-slot:head>

  <div class="shell">
    <section class="hero">
      <div class="eyebrow">Normattiva MCP App</div>
      <h1 class="title" id="act-title">In attesa del dettaglio dell'atto</h1>
      <p class="subtitle" id="act-subtitle">
        Quando il tool restituisce il payload, qui compare il testo HTML dell'articolo e puoi copiare l'URN o aprire la pagina pubblica su Normattiva.
      </p>

      <div class="actions">
        <button class="button button-primary" id="copy-urn-btn" type="button" disabled>Copia URN</button>
        <button class="button button-secondary" id="open-normattiva-btn" type="button" disabled>Apri su Normattiva</button>
      </div>

      <div class="badge-row" id="badge-row" hidden></div>
    </section>

    <div class="grid">
      <section class="panel">
        <h2>Articolo</h2>
        <div class="article" id="article-html">
          <div class="empty-state">Esegui il tool di dettaglio per visualizzare qui il contenuto HTML dell'atto.</div>
        </div>
      </section>

      <aside class="panel">
        <h3>Metadati</h3>
        <div class="meta-list" id="meta-list">
          <div class="meta-row">
            <div class="meta-label">URN</div>
            <div class="meta-value" id="urn-value">Nessun URN ancora disponibile</div>
          </div>
          <div class="meta-row">
            <div class="meta-label">Stato</div>
            <div class="meta-value" id="status-value">In attesa di risposta dal server</div>
          </div>
        </div>
      </aside>
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
