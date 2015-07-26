<?php
namespace app\components;

use Yii;

    abstract class SharedMemoryAbstraction
    {
        protected $_id;

        public function __construct($id)
        {
            $this->_id = $id;
        }

        abstract public function checkData();
        abstract public function getData();
    }