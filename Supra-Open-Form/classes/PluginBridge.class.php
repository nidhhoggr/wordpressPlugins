<?php

require_once(dirname(__FILE__).'/../SupraOpenForm_Plugin.php');

class PluginBridge {

    private $aPlugin;

    function __construct() {
        $this->aPlugin = new SupraOpenForm_Plugin();
    }

    public function getTablePrefix($table_name) {
        return $this->aPlugin->prefixTableName($table_name);
    }

    public function getMetaOption($name) {
        return $this->aPlugin->getOption($name);
    }
}
