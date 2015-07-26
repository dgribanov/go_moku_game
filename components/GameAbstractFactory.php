<?php
namespace app\components;

    class GameAbstractFactory
    {
        public static function getListener($listenerType, SharedMemoryAbstraction $sharedMemory) {
            switch($listenerType)
            {
                case ('long polling'):
                    $listener = new LongPollingGame($sharedMemory);
                    break;
            }
            return $listener;
        }
    }