<?php

use franciscoblancojn\wordpress_utils\FWUSystemLog;

class DPAI_DUPLICADOS
{
    public static function getPrompt($post_id, $prompt, $customFields)
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
    public static function getDuplicadosByPrompt($PROMPT)
    {
        $jsonResponse = [];
        try {
            $result = DPAI_AI::sendPrompt($PROMPT);
            
            if ($result['status'] == 'error') {
                return $result;
            }
            $result['message'] = "Duplicados Generado";
            $result['data'] = DPAI_AI::parseJson($result['data']);

            return $result;
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
            return $error;
        }
    }

    public static function getDuplicados($post_id, $prompt, $customFields)
    {
        $jsonResponse = [];
        try {
            $PROMPT = self::getPrompt($post_id, $prompt, $customFields);
            $result = self::getDuplicadosByPrompt($PROMPT);
            return $result;
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
            return $error;
        }
    }
}
