<?php

use franciscoblancojn\wordpress_utils\FWUSystemLog;

class DPAI_USE_DATA_DUPLICADOS extends DPAI_USE_DATA_BASE
{
    protected $KEY = DPAI_DUPLICADOS;

    public function deletePost($post_id)
    {
        $DUPLICADOS = $this->get();
        if (isset($DUPLICADOS[$post_id])) {
            unset($DUPLICADOS[$post_id]);
            $this->set($DUPLICADOS);
        }
    }
    public function deletePrompt($post_id, $prompt)
    {
        $DUPLICADOS = $this->get();
        if (isset($DUPLICADOS[$post_id]['variations'][$prompt])) {
            unset($DUPLICADOS[$post_id]['variations'][$prompt]);
            $this->set($DUPLICADOS);
            if (count($DUPLICADOS[$post_id]['variations']) == 0) {
                $this->deletePost($post_id);
            }
        }
    }
    public function deleteVariation($post_id, $prompt, $v)
    {
        $DUPLICADOS = $this->get();
        if (isset($DUPLICADOS[$post_id]['variations'][$prompt][$v])) {
            unset($DUPLICADOS[$post_id]['variations'][$prompt][$v]);
            $DUPLICADOS[$post_id]['variations'][$prompt] = array_values(
                $DUPLICADOS[$post_id]['variations'][$prompt]
            );
            $this->set($DUPLICADOS);
            if (count($DUPLICADOS[$post_id]['variations'][$prompt]) == 0) {
                $this->deletePrompt($post_id, $prompt);
            }
        }
    }
    public function generateVariation($post_id, $prompt, $v)
    {
        $DUPLICADOS = $this->get();
        if (isset($DUPLICADOS[$post_id]['variations'][$prompt][$v])) {
            $this->deleteVariation($post_id, $prompt, $v);
        }
    }
}
