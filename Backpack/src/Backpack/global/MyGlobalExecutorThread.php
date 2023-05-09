<?php

declare(strict_types=1);

namespace Backpack\global;

use Backpack\BACKPACK;
use Illuminate\Database\Capsule\Manager;
use pocketmine\Server;
use pocketmine\snooze\SleeperNotifier;
use poggit\virion\devirion\DEVirion;
use Webmozart\PathUtil\Path;
use xerenahmed\database\global\GlobalExecutorThread;
use xerenahmed\database\HandlerQueue;

class MyGlobalExecutorThread extends GlobalExecutorThread
{

    public function createCapsule(): Manager
    {
        return BACKPACK::newCapsule($this->dataPath);
    }

    public function registerClassLoaders(): void
    {
        parent::registerClassLoaders();

        require_once Path::join($this->dataPath, "vendor/autoload.php");
    }

    public function __construct(HandlerQueue $queue, SleeperNotifier $notifier)
    {
        parent::__construct($queue, $notifier);

        /** @var DEVirion $DEVirion */
        $DEVirion = Server::getInstance()->getPluginManager()->getPlugin("DEVirion");
        $this->setClassLoaders([
            Server::getInstance()->getLoader(),
            $DEVirion->getVirionClassLoader()
        ]);
    }
}
