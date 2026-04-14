<?php

use franciscoblancojn\wordpress_utils\FWUSystemLog;

class DPAI_WP_JSON
{
    public static function init()
    {
        register_rest_route(DPAI_KEY, '/get-custom-fields', [
            'methods' => 'GET',
            'callback' => [self::class, 'getCustomFieldsEnpoint'],
        ]);
        register_rest_route(DPAI_KEY, '/set-custom-fields', [
            'methods' => 'POST',
            'callback' => [self::class, 'setCustomFieldsEnpoint'],
        ]);
    }

    public static function getCustomFields($post_id)
    {
        if (!get_post($post_id)) {
            return [
                'success' => false,
                'message' => 'Post no existe'
            ];
        }
        $result = [];
        $content = get_post_field('post_content', $post_id);
        preg_match_all('/{{(.*?)}}/', $content, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $key) {
                if (strpos($key, DPAI_KEY) !== 0) continue;
                $value = get_post_meta($post_id, $key, true);
                $result[$key] = $value;
            }
        }
        return $result;
    }
    public static function getCustomFieldsEnpoint($request)
    {
        $post_id = $request->get_param('post_id');
        if (!$post_id) {
            return [
                'success' => false,
                'message' => 'post_id es requerido'
            ];
        }
        $post_id = intval($post_id);
        return self::getCustomFields($post_id);
    }
    public static function setCustomFields($post_id, $data)
    {
        $result = [];
        // Validar post_id
        if (empty($post_id)) {
            return [
                'success' => false,
                'message' => 'post_id es requerido'
            ];
        }
        $post_id = intval($post_id);
        if (!get_post($post_id)) {
            return [
                'success' => false,
                'message' => 'Post no existe'
            ];
        }
        // Recorrer campos
        foreach ($data as $key => $value) {

            // Solo permitir campos que empiecen con DPAI_
            if (strpos($key, DPAI_KEY) !== 0) continue;

            // Sanitizar (puedes mejorar según tipo)
            $sanitized = is_array($value)
                ? array_map('sanitize_text_field', $value)
                : sanitize_text_field($value);

            update_post_meta($post_id, $key, $sanitized);

            $result[$key] = $sanitized;
        }


        FWUSystemLog::add(DPAI_KEY, [
            'type' => "setCustomFields",
            'data' => $data,
            'result' => $result,
        ]);
        return $result;
    }
    public static function setCustomFieldsEnpoint($request)
    {
        $data = $request->get_json_params();
        return self::setCustomFields($data['post_id'], $data);
    }
}

add_action('rest_api_init', ['DPAI_WP_JSON', 'init']);
