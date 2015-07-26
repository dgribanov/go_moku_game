<?php
namespace app\components;

use Yii;
use yii\base\Component;

    class GameComponent extends Component
    {
        private $_listenerType;

        public function __construct($config = [])
        {
            $this->_listenerType = Yii::$app->params['gameListenerType'];
            parent::__construct($config);
        }

        public function startGameListener($gameId)
        {
            $listener = GameAbstractFactory::getListener(
                $this->_listenerType,
                SharedMemoryAbstractFactory::getSharedMemory($gameId)
            );
            $listener->startListener();

        }
    }