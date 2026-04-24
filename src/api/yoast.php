<?php

use franciscoblancojn\wordpress_utils\FWUSystemLog;

class DPAI_YOAST
{
    public static function init()
    {
        register_rest_route(DPAI_KEY, '/yoast/get', [
            'methods' => 'GET',
            'callback' => [self::class, 'GET_Enpoint'],
        ]);
        register_rest_route(DPAI_KEY, '/yoast/set', [
            'methods' => 'POST',
            'callback' => [self::class, 'SET_Enpoint'],
        ]);
    }
    public static  function GET($post_id)
    {
        $all_meta = get_post_meta($post_id);
        $yoast = [];

        foreach ($all_meta as $key => $value) {
            if (strpos($key, '_yoast_wpseo_') === 0) {
                $yoast[$key] = maybe_unserialize($value[0]);
            }
        }

        return $yoast;
    }
    public static function GET_Enpoint($request)
    {
        $post_id = $request->get_param('post_id');
        if (!$post_id) {
            return [
                'success' => false,
                'message' => 'post_id es requerido'
            ];
        }
        $post_id = intval($post_id);
        return self::GET($post_id);
    }
    public static function SET($post_id, $data)
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
        FWUSystemLog::add(DPAI_KEY, [
            'type' => "DPAI_YOAST SET",
            'data' => $data,
            'result' => $result,
        ]);
        return $result;
    }
    public static function SET_Enpoint($request)
    {
        $data = $request->get_json_params();
        return self::SET($data['post_id'], $data);
    }
}

// add_action('rest_api_init', ['DPAI_YOAST', 'init']);
