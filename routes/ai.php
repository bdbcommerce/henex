<?php

use App\Http\Middleware\AuthenticateMcpToken;
use App\Mcp\Servers\HenexServer;
use Laravel\Mcp\Facades\Mcp;

// Claude Code / Desktop:
//   claude mcp add --transport http henex https://henex.uz/mcp --header "Authorization: Bearer <MCP_TOKEN>"
Mcp::web('/mcp', HenexServer::class)
    ->middleware([AuthenticateMcpToken::class, 'throttle:120,1']);
