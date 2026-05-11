<?php

declare(strict_types = 1);

use App\Mcp\Servers\NormattivaServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/normattiva', NormattivaServer::class);
