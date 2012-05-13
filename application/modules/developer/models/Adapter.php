<?php

class Developer_Model_Adapter {

    private $_directoryList = array();
    private $_searchDirectory = '';

    public function __construct() {
        $this->_searchDirectory = 'application' . DIRECTORY_SEPARATOR;

        $this->_directoryList = array();
        $this->getDirectoryList($this->_searchDirectory);
    }

    public function getRecords() {
        $files = $this->getFileList('phtml');

        $data = array();
        foreach ($files as $value) {
            $filename = substr($value, strlen($this->_searchDirectory));
            $time = filemtime($value);
            $ext = pathinfo($value, PATHINFO_EXTENSION);
            $data[] = (Object)array(
                'filename' => $filename,
                'datetime' => $time,
                'ext' => $ext,
                'fullfilename' => $value
            );
        }
        
        return $data;
    }

    public function getFileList($ext) {
        $result = array();
        foreach ($this->_directoryList as $path) {
            if (is_dir($path)) {
                if ($dh = opendir($path)) {
                    while (false !== ($file = readdir($dh))) {
                        if (is_file($path . $file) && (strtolower(pathinfo($file, PATHINFO_EXTENSION)) == $ext)) {
                            $result[] = $path . $file;
                        }
                    }
                    closedir($dh);
                }
            }
        }
        return $result;
    }

    public function getDirectoryList($path) {
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while (false !== ($dir = readdir($dh))) {
                    if (is_dir($path . $dir) && ($dir !== '.') && ($dir !== '..')) {
                        $subdir = $path . $dir . DIRECTORY_SEPARATOR;
                        $this->_directoryList[] = $subdir;
                        $this->getDirectoryList($subdir);
                    }
                }
                closedir($dh);
            }
        }
    }
    
    public function getRastorTablePaginatorAdapter($order, $orderDirection){
        return new Zend_Paginator_Adapter_Array($this->getRecords());
    }

}
