<?php

use franciscoblancojn\wordpress_utils\FWUSystemLog;

class DPAI_CONFIG
{
    public $CONFIG = [];
    public function __construct()
    {
        $this->init();
    }
    public function init() {
        $this->onLoadConfig();
    }

    private function onLoadConfig()
    {
        $this->CONFIG = get_option(DPAI_CONFIG, []);
    }
    private function onSaveConfig()
    {
        update_option(DPAI_CONFIG, $this->CONFIG);
    }
    public function getConfig()
    {
        return $this->CONFIG;
    }
    public function setData($key,$value)
    {
        $this->CONFIG[$key] = $value;
        $this->onSaveConfig();
    }
    public function setConfig($CONFIG)
    {
        $this->CONFIG = $CONFIG;
        $this->onSaveConfig();
    }
}
