<?php

require_once __DIR__ . '/vendor/autoload.php';

use McpServer\McpServer;

// MCP 서버 실행
$server = new McpServer();

// STDIO를 통한 통신
while (true) {
    $input = fgets(STDIN);
    if ($input === false) break;
    
    $input = trim($input);
    if (empty($input)) continue;
    
    $response = $server->handleMessage($input);
    echo json_encode($response) . "\n";
    flush();
}