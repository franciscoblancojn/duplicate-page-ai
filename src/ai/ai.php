<?php

use franciscoblancojn\wordpress_utils\FWUSystemLog;

class DPAI_AI
{
    private static function getConfig()
    {
        $DPAI_CONFIG = new DPAI_CONFIG();
        return $DPAI_CONFIG->getConfig();
    }
    public static function generatePrompt($post_id, $prompt, $customFields)
    {
        $title = get_the_title($post_id);
        $content = get_post_field('post_content', $post_id);
        $PROMPT = "
            ----TITULO DE LA PAGINA----
            " . $title . "
            ----CONTENIDO DE LA PAGINA----
            " . $content . "
            ----CAMPOS PERSONALIZADOS----
            " . json_encode($customFields) . "
            ----PROMP BASE----
            " . $prompt . "
            ----
            Necesito que generes un json basandote en el contenido y cambos personalizados como referencia.
            Formato de json : {title:'title',customFields:{key:'value',...}}
            En caso que se pidan varias respuesta este es el formato a usar:
            Formato de array : [{title:'title',customFields:{key:'value',...}},{title:'title2',customFields:{key:'value',...}}]
            Importante, ten en cuenta el prompt base.
        ";
        return $PROMPT;
    }
    public static function generateDuplicatos($post_id, $prompt, $customFields)
    {
        $jsonResponse = [];
        try {
            $CONFIG = self::getConfig();
            $PROMPT = self::generatePrompt($post_id, $prompt, $customFields);
            // 1. Configuración de parámetros
            $apiKey = $CONFIG['apikey']; // Reemplaza con tu clave real
            $modelo = $CONFIG['modelo'] ?? DPAI_CONFIG_MODEL_DEFAULT;
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelo}:generateContent?key={$apiKey}";

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

            // 3. Configuración de cURL
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

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

            // 6. Extraer el texto de la respuesta siguiendo la estructura de la API
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
                throw new \RuntimeException('Error en cURL: ' . curl_error($ch));
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
}
