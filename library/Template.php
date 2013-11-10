<?php

namespace StoredLibrary;

class Template  {

    private $file;
    private $values = array();
    private static $_instance;

    public function setFile($file) {
        $this->file = $file;
    }

    public function _set($data) {
        foreach($data as $key => $value) {
            $this->values[$key] = $value;
        }
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getValues()
    {
        return $this->values;
    }

    public static function getInstance() {
        if(!isset($_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function display($file, $arraySettings = '') {
        $tpl = Template::getInstance();
        if(!empty($file)) {
            $tpl->setFile($file);
            if(!empty($arraySettings)) {
                $tpl->_set($arraySettings);
            }
        }
        if (!file_exists($tpl->getFile())) {
            throw new \Exception("Error loading template file ". $tpl->getFile());
        }
        $output = file_get_contents($tpl->getFile());
        foreach ($tpl->getValues() as $key => $value) {
            $tagToReplace = "[@$key]";
            $output = str_replace($tagToReplace, $value, $output);
        }
        return $output;
    }
}

