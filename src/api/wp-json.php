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
    private static function extractKeys($data, &$keys)
{
    if (is_array($data)) {
        foreach ($data as $key => $value) {

            // 🔥 también revisar claves (por si acaso)
            if (is_string($key)) {
                self::extractKeys($key, $keys);
            }

            self::extractKeys($value, $keys);
        }

    } elseif (is_object($data)) {

        foreach ((array)$data as $value) {
            self::extractKeys($value, $keys);
        }

    } elseif (is_string($data)) {

        if (preg_match_all('/{{(.*?)}}|__(.*?)__/', $data, $matches)) {

            $foundKeys = array_merge(
                array_filter($matches[1]),
                array_filter($matches[2])
            );

            foreach ($foundKeys as $key) {
                $keys[] = trim($key);
            }
        }
    }
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
        $keys = [];

        // 🔹 1. Contenido normal (post_content)
        $content = get_post_field('post_content', $post_id);

        if ($content) {
            self::extractKeys($content, $keys);
        }

        // 🔹 2. Elementor (_elementor_data)
        $elementor_data = get_post_meta($post_id, '_elementor_data', true);

        if ($elementor_data) {
            $data = json_decode($elementor_data, true);

            if (is_array($data)) {
                self::extractKeys($data, $keys);
            }
        }

        // 🔥 3. Eliminar duplicados
        $keys = array_unique($keys);

        // 🔹 4. Obtener valores
        foreach ($keys as $key) {
            $value = get_post_meta($post_id, $key, true);
            $result[$key] = $value;
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
