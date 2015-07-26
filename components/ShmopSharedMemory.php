<?php
namespace app\components;

use Yii;

    class ShmopSharedMemory extends SharedMemoryAbstraction
    {
        private $_shm_id;

        public function __construct($id)
        {
            parent::__construct($id);
            $this->__init();

        }

        private function __init()
        {
            $this->_shm_id = shmop_open($this->_id, "c", 0644, 11);
            shmop_write($this->_shm_id, $this->_id, 0);
        }

        public function __destruct()
        {
            shmop_delete ($this->_shm_id);
        }

        public function checkData()
        {
            $id = shmop_read($this->_shm_id, 0, shmop_size($this->_shm_id));
            return $id == $this->_id;
        }

        public function getData()
        {
            $id = shmop_read($this->_shm_id, 0, shmop_size($this->_shm_id));
            shmop_close ($this->_shm_id);
            return $id;
        }
    }