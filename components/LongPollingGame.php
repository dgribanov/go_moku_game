<?php
namespace app\components;

    class LongPollingGame extends GameAbstraction
    {
        public function runListener() {
            set_time_limit(0);
            while (true) {
                if ($this->_memory->checkData()) {
                    sleep(1);
                    continue;
                } else {
                    return $this->_memory->getData();
                }
            }

        }
    }