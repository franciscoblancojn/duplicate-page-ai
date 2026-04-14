<?php

add_filter('the_content', function ($content) {
    preg_match_all('/{{(.*?)}}/', $content, $matches);

    if (!empty($matches[1])) {
        foreach ($matches[1] as $key) {
            $value = get_post_meta(get_the_ID(), $key, true);
            if (isset($value) && $value !='') {
                $content = str_replace('{{' . $key . '}}', $value, $content);
            }
        }
    }

    return $content;
});
