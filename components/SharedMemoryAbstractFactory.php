<?php
namespace app\components;

    class SharedMemoryAbstractFactory
    {
        public static function getSharedMemory($gameId) {
            switch(true)
            {
                case (function_exists('shmop_open')):
                    $memory = new ShmopSharedMemory($gameId);
                    break;
                default:
                    $memory = new FileSharedMemory($gameId);
            }
            return $memory;
        }
    }