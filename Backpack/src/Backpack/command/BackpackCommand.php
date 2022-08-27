<?php

namespace Backpack\command;

use Backpack\inventory\BackpackInventory;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class BackpackCommand extends Command
{
    public function __construct()
    {
        parent::__construct("backpack", "Open your backpack!");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($sender instanceof Player){
            new BackpackInventory($sender);
        }
    }
}