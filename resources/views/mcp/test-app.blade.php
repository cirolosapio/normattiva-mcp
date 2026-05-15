<x-mcp::app :title="$title">
  <x-slot:head>
    <script type="module">
      createMcpApp(async (app) => {
        document.getElementById('run-btn').addEventListener('click', async () => {
          const result = await app.callServerTool('test', {});
          document.getElementById('output').textContent = result.content[0]?.text ?? '';
        });
      });
    </script>
  </x-slot:head>

  <div id="app">
    <button id="run-btn">Run</button>
    <pre id="output"></pre>
  </div>
</x-mcp::app>
