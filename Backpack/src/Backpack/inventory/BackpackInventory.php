<?php

namespace Backpack\inventory;

use Backpack\provider\SQLite;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\player\Player;

class BackpackInventory
{
    public function __construct(Player $player)
    {
        $result = 0;
        $data = SQLite::getDatabase();
        $inventory = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $inventory->setName("§lYOUR BACKPACK");
        if ($data->isDataControl($player->getName())) {
            $items = $data->decode($player);
            $inventory->getInventory()->setContents($items);
            $result = true;
        }
        $inventory->setInventoryCloseListener(function () use ($player, $result, $data, $inventory): void {
            $items = $data->encode($inventory->getInventory()->getContents());
            switch ($result) {
                case true:
                    $data->updateBackpackData($player->getName(), $items);
                    break;
                default:
                    $data->createBackpackData($player->getName(), $items);
                    break;
            }
            $player->sendMessage("§8» §aItems in your backpack have been saved!");
        });
        $inventory->send($player);
    }
}