<?php

add_filter('the_content', function ($content) {
    preg_match_all('/{{(.*?)}}/', $content, $matches);

    if (!empty($matches[1])) {
        foreach ($matches[1] as $key) {
            $value = null;
            // 1. Si existe GET y estamos en admin
            if (current_user_can( 'manage_options' ) && isset($_GET[$key]) && $_GET[$key] !== '') {
                $value = sanitize_text_field($_GET[$key]);
            }
            // 2. Si no, usar postmeta
            else {
                $value = get_post_meta(get_the_ID(), $key, true);
            }
            // 3. Reemplazo
            if (!empty($value)) {
                $content = str_replace('{{' . $key . '}}', $value, $content);
            }
        }
    }

    return $content;
});
