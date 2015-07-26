<?php
namespace app\components;

    abstract class GameAbstraction
    {
        protected $_memory;

        public function __construct(SharedMemoryAbstraction $sharedMemory)
        {
            $this->_memory = $sharedMemory;
        }

        abstract public function runListener();
    }