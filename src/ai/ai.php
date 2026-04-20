<?php

use franciscoblancojn\wordpress_utils\FWUSystemLog;

class DPAI_AI
{
    private static function getConfig()
    {
        $DPAI_CONFIG = new DPAI_CONFIG();
        return $DPAI_CONFIG->getConfig();
    }
    private static function request(
        $url,
        $method = "GET",
        $data = null,
    ) {
        $jsonResponse = [];
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if ($method == "POST") {
                curl_setopt($ch, CURLOPT_POST, true);
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            if (isset($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }

            // 4. Ejecución y manejo de errores
            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                throw new \RuntimeException('Error en cURL: ' . curl_error($ch));
            }

            curl_close($ch);

            // 5. Decodificar la respuesta
            $jsonResponse = json_decode($response, true);

            if (isset($jsonResponse['error'])) {
                throw new \RuntimeException('Error: ' . $jsonResponse['error']['message']);
            }
            return [
                "status" => "ok",
                "message" => "Respuesta Exitosa",
                'data' => $jsonResponse
            ];;
        } catch (\Throwable $th) {
            $error = [
                "status" => "error",
                "message" => $th->getMessage(),
                'data' => [
                    'line' => $th->getLine(),
                    'file' => $th->getFile(),
                    'jsonResponse' => $jsonResponse
                ]
            ];
            FWUSystemLog::add(DPAI_KEY, [
                'type' => "IA error",
                'data' => $error
            ]);
            return $error;
        }
    }
    public static function sendPrompt($PROMPT)
    {
        $jsonResponse = [];
        try {
            $CONFIG = self::getConfig();
            // 1. Configuración de parámetros
            $apiKey = $CONFIG['apikey']; // Reemplaza con tu clave real
            $modelo = $CONFIG['modelo'];
            $url = "https://generativelanguage.googleapis.com/v1/models/{$modelo}:generateContent?key={$apiKey}";

            // 2. Estructura del cuerpo de la petición (JSON)
            $data = [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $PROMPT]
                        ]
                    ]
                ]
            ];

            $result = self::request($url,"POST", $data);
            if ($result['status'] == 'error') {
                return $result;
            }
            $jsonResponse = $result['data'];
            // 3. Extraer el texto de la respuesta siguiendo la estructura de la API
            if (isset($jsonResponse['candidates'][0]['content']['parts'][0]['text'])) {
                $result = $jsonResponse['candidates'][0]['content']['parts'][0]['text'];
                $data = json_decode($result, true);
                return [
                    "status" => "ok",
                    "message" => "Respuesta Exitosa",
                    'result' => $result,
                    'data' => $data,
                ];
            } else {
                throw new \RuntimeException('Error en cURL');
            }
        } catch (\Throwable $th) {
            $error = [
                "status" => "error",
                "message" => $th->getMessage(),
                'data' => [
                    'line' => $th->getLine(),
                    'file' => $th->getFile(),
                    'jsonResponse' => $jsonResponse
                ]
            ];
            FWUSystemLog::add(DPAI_KEY, [
                'type' => "IA error",
                'data' => $error
            ]);
            return $error;
        }
    }
    public static function getModels()
    {
        $jsonResponse = [];

        try {
            $CONFIG = self::getConfig();

            $apiKey = $CONFIG['apikey'];

            // Endpoint para listar modelos
            $url = "https://generativelanguage.googleapis.com/v1/models?key={$apiKey}";

            $result = self::request($url);
            if ($result['status'] == 'error') {
                return $result;
            }
            $jsonResponse = $result['data'];

            $models = [];

            if (!empty($jsonResponse['models']) && is_array($jsonResponse['models'])) {
                foreach ($jsonResponse['models'] as $model) {

                    $methods = $model['supportedGenerationMethods'] ?? [];

                    // Filtrar solo los que soportan generateContent
                    if (!in_array('generateContent', $methods)) {
                        continue;
                    }

                    $models[] = [
                        'name' => $model['name'],
                        'model' => str_replace('models/', '', $model['name']),
                        'displayName' => $model['displayName'] ?? $model['name'],
                    ];
                }
            }

            return [
                "status" => "ok",
                "message" => "Modelos obtenidos correctamente",
                "data" => $models,
            ];
        } catch (\Throwable $th) {
            $error = [
                "status" => "error",
                "message" => $th->getMessage(),
                'data' => [
                    'line' => $th->getLine(),
                    'file' => $th->getFile(),
                    'jsonResponse' => $jsonResponse
                ]
            ];

            FWUSystemLog::add(DPAI_KEY, [
                'type' => "IA modelos error",
                'data' => $error
            ]);

            return $error;
        }
    }
}
