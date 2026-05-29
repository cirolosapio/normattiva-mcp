# normattiva-mcp

An MCP (Model Context Protocol) server that exposes the Italian legislative database [Normattiva](https://www.normattiva.it) to AI assistants. Built with Laravel 13 and deployed serverlessly on AWS Lambda via [Bref](https://bref.sh).

## Overview

This project wraps the Normattiva public API into a set of MCP tools, making it possible for AI assistants (Claude, Cursor, etc.) to search, retrieve, and read Italian legislation in context — without manual browsing.

The server is publicly available at:

```
https://api.losapio.dev/mcp/normattiva
```

## Available Tools

| Tool | Description |
|---|---|
| `search-acts-simple` | Full-text search across legislative acts |
| `search-acts-advanced` | Structured search with filters (type, year, date range…) |
| `get-act-details-by-urn` | Retrieve an act or specific article by URN NIR (e.g. `urn:nir:stato:legge:2001-12-28;448~art2`) |
| `get-act-details` | Retrieve an act using redactional code and GU date |
| `search-updated-acts` | List acts updated within a given date/time interval |
| `get-act-denominations` | List available act denominations and their codes |
| `get-available-extensions` | List available download formats |
| `get-provision-class-types` | List provision class types and codes |

## Stack

- **PHP 8.5** / **Laravel 13**
- **[laravel/mcp](https://github.com/laravel/mcp)** — official MCP server package for Laravel
- **[Bref](https://bref.sh)** — PHP runtime for AWS Lambda
- **Serverless Framework** — infrastructure and deployment
- **AWS Lambda** (arm64, `eu-central-1`)
- **SSM Parameter Store** — secrets management (`APP_KEY`, `APP_URL`, `SENTRY_LARAVEL_DSN`)
- **Sentry** — error tracking

## Connect to Claude

### Claude Desktop / Claude.ai

Add the following to your MCP configuration:

```json
{
  "mcpServers": {
    "normattiva": {
      "type": "url",
      "url": "https://api.losapio.dev/mcp/normattiva"
    }
  }
}
```

For Claude Desktop, edit `claude_desktop_config.json` (usually found in `~/Library/Application Support/Claude/` on macOS or `%APPDATA%\Claude\` on Windows).

### Other MCP clients

Any client that supports the MCP SSE transport can connect to `https://api.losapio.dev/mcp/normattiva`.

## Local Development

### Requirements

- PHP 8.5+
- Composer

### Setup

```bash
git clone https://github.com/cirolosapio/normattiva-mcp.git
cd normattiva-mcp

composer install

cp .env.example .env
php artisan key:generate

php artisan serve
```

## Deployment

The project deploys to AWS Lambda using the Serverless Framework and Bref.

```bash
serverless deploy
```

Secrets are read from SSM Parameter Store at deploy time. Make sure the following parameters exist under `/<service-name>/`:

- `APP_KEY`
- `APP_URL`
- `SENTRY_LARAVEL_DSN`
