<?php
// src\McpServer.php

namespace McpServer;

class McpServer
{
    private $tools = [];
    private $resources = [];

    public function __construct()
    {
        $this->registerTools();
        $this->registerResources();
    }

    /**
     * 도구(Tools) 등록
     */
    private function registerTools()
    {
        $this->tools = [
            [
                'name' => 'get_weather',
                'description' => '지정된 도시의 날씨 정보를 가져옵니다',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'city' => [
                            'type' => 'string',
                            'description' => '날씨를 조회할 도시명'
                        ]
                    ],
                    'required' => ['city']
                ]
            ],
            [
                'name' => 'calculate',
                'description' => '간단한 수학 계산을 수행합니다',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'expression' => [
                            'type' => 'string',
                            'description' => '계산할 수식 (예: 2+2, 10*5)'
                        ]
                    ],
                    'required' => ['expression']
                ]
            ]
        ];
    }

    /**
     * 리소스(Resources) 등록
     */
    private function registerResources()
    {
        $this->resources = [
            [
                'uri' => 'file://logs/app.log',
                'name' => 'Application Logs',
                'description' => '애플리케이션 로그 파일',
                'mimeType' => 'text/plain'
            ]
        ];
    }

    /**
     * MCP 메시지 처리
     */
    public function handleMessage($message)
    {
        $request = json_decode($message, true);
        
        if (!$request || !isset($request['method'])) {
            return $this->createErrorResponse('Invalid request format');
        }

        switch ($request['method']) {
            case 'initialize':
                return $this->handleInitialize($request);
            case 'tools/list':
                return $this->handleToolsList($request);
            case 'tools/call':
                return $this->handleToolCall($request);
            case 'resources/list':
                return $this->handleResourcesList($request);
            case 'resources/read':
                return $this->handleResourceRead($request);
            default:
                return $this->createErrorResponse('Unknown method: ' . $request['method']);
        }
    }

    /**
     * 초기화 처리
     */
    private function handleInitialize($request)
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $request['id'] ?? null,
            'result' => [
                'protocolVersion' => '2024-11-05',
                'capabilities' => [
                    'tools' => ['listChanged' => true],
                    'resources' => ['subscribe' => true, 'listChanged' => true]
                ],
                'serverInfo' => [
                    'name' => 'PHP MCP Server',
                    'version' => '1.0.0'
                ]
            ]
        ];
    }

    /**
     * 도구 목록 반환
     */
    private function handleToolsList($request)
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $request['id'] ?? null,
            'result' => [
                'tools' => $this->tools
            ]
        ];
    }

    /**
     * 도구 실행
     */
    private function handleToolCall($request)
    {
        $toolName = $request['params']['name'] ?? '';
        $arguments = $request['params']['arguments'] ?? [];

        switch ($toolName) {
            case 'get_weather':
                return $this->executeWeatherTool($request, $arguments);
            case 'calculate':
                return $this->executeCalculateTool($request, $arguments);
            default:
                return $this->createErrorResponse('Unknown tool: ' . $toolName, $request['id'] ?? null);
        }
    }

    /**
     * 날씨 도구 실행
     */
    private function executeWeatherTool($request, $arguments)
    {
        $city = $arguments['city'] ?? '';
        
        // 실제로는 외부 API를 호출하겠지만, 여기서는 더미 데이터 반환
        $weatherData = [
            'city' => $city,
            'temperature' => rand(15, 35) . '°C',
            'condition' => ['맑음', '흐림', '비', '눈'][rand(0, 3)],
            'humidity' => rand(30, 90) . '%'
        ];

        return [
            'jsonrpc' => '2.0',
            'id' => $request['id'] ?? null,
            'result' => [
                'content' => [
                    [
                        'type' => 'text',
                        'text' => sprintf(
                            "%s의 현재 날씨:\n온도: %s\n날씨: %s\n습도: %s",
                            $weatherData['city'],
                            $weatherData['temperature'],
                            $weatherData['condition'],
                            $weatherData['humidity']
                        )
                    ]
                ]
            ]
        ];
    }

    /**
     * 계산 도구 실행
     */
    private function executeCalculateTool($request, $arguments)
    {
        $expression = $arguments['expression'] ?? '';
        
        try {
            // 보안상 위험할 수 있으므로 실제 구현시에는 더 안전한 방법 사용
            $result = eval("return $expression;");
            
            return [
                'jsonrpc' => '2.0',
                'id' => $request['id'] ?? null,
                'result' => [
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => "$expression = $result"
                        ]
                    ]
                ]
            ];
        } catch (Exception $e) {
            return $this->createErrorResponse('계산 오류: ' . $e->getMessage(), $request['id'] ?? null);
        }
    }

    /**
     * 리소스 목록 반환
     */
    private function handleResourcesList($request)
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $request['id'] ?? null,
            'result' => [
                'resources' => $this->resources
            ]
        ];
    }

    /**
     * 리소스 읽기
     */
    private function handleResourceRead($request)
    {
        $uri = $request['params']['uri'] ?? '';
        
        if ($uri === 'file://logs/app.log') {
            $logContent = "2024-01-15 10:30:00 INFO: 애플리케이션 시작\n";
            $logContent .= "2024-01-15 10:30:01 INFO: 데이터베이스 연결 성공\n";
            $logContent .= "2024-01-15 10:30:02 INFO: 서버 준비 완료\n";
            
            return [
                'jsonrpc' => '2.0',
                'id' => $request['id'] ?? null,
                'result' => [
                    'contents' => [
                        [
                            'uri' => $uri,
                            'mimeType' => 'text/plain',
                            'text' => $logContent
                        ]
                    ]
                ]
            ];
        }

        return $this->createErrorResponse('Resource not found: ' . $uri, $request['id'] ?? null);
    }

    /**
     * 오류 응답 생성
     */
    private function createErrorResponse($message, $id = null)
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'error' => [
                'code' => -1,
                'message' => $message
            ]
        ];
    }
}

// 서버 실행 스크립트
class McpServerRunner
{
    public function run()
    {
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
    }
}

// CLI에서 직접 실행시
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $runner = new McpServerRunner();
    $runner->run();
}