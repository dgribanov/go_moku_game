<?php
namespace app\components;

use Yii;

    class FileSharedMemory extends SharedMemoryAbstraction
    {
        private $_filename;
        private $_path;
        private $_lastModTime;

        public function __construct($id)
        {
            parent::__construct($id);
            $this->__init();

        }

        private function __init()
        {
            $this->_filename = 'game_' . $this->_id . '.txt';
            $this->_path = Yii::getAlias('@data/' . $this->_filename);
            $handle = fopen($this->_path, 'wb');
            fwrite($handle, $this->_id);
            fclose ($handle);
            $this->_lastModTime = filemtime($this->_path);
        }

        public function __destruct()
        {
            unlink($this->_path);
        }

        public function checkData()
        {
            clearstatcache();
            $modTime = filemtime($this->_path);
            return $modTime == $this->_lastModTime;
        }

        public function getData()
        {
            $handle = fopen($this->_path, "rb");
            $id = fread($handle, filesize($this->_path));
            fclose($handle);
            return $id;
        }
    }