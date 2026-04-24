<?php

use franciscoblancojn\wordpress_utils\FWUSystemLog;

class DPAI_DUPLICADOS
{
    public static function getPrompt($post_id, $prompt, $customFields, $yoastFields)
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
            ----DATOS DE YOAST SEO----
            " . json_encode($yoastFields) . "
            ----PROMP BASE----
            " . $prompt . "
            ----
            Necesito que generes un json basandote en el contenido, campos personalizados y datos de yoast seo como referencia.
            Formato de json : {title:'title',customFields:{key:'value',...},yoastFields:{key:'value',...}}
            En caso que se pidan varias respuesta este es el formato a usar:
            Formato de array : [{title:'title',customFields:{key:'value',...},yoastFields:{key:'value',...}},{title:'title2',customFields:{key:'value',...},yoastFields:{key:'value',...}}]
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
            $result['message'] = "Duplicados Generados";
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

    public static function getDuplicados($post_id, $prompt, $customFields, $yoastFields)
    {
        $jsonResponse = [];
        try {
            $PROMPT = self::getPrompt($post_id, $prompt, $customFields, $yoastFields);
            $result = self::getDuplicadosByPrompt($PROMPT);
            FWUSystemLog::add(DPAI_KEY, [
                'type' => "IA Duplicados result",
                'post_id' => $post_id,
                'prompt' => $prompt,
                'customFields' => $customFields,
                'yoastFields' => $yoastFields,
                'result' => $result,
            ]);
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
