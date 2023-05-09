<?php

namespace Backpack\inventory;

use Backpack\global\MyGlobalExecutor;
use Backpack\models\BackpackModel;
use Generator;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\item\Item;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;

class BackpackInventory
{

    public function __construct(Player $player)
    {
        $inventory = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $inventory->setName("§lYOUR BACKPACK");
        $this->setBackpack($player->getName(), $inventory);
        $inventory->setInventoryCloseListener(function () use ($player, $inventory): void {
            $items = $this->encode($inventory->getInventory()->getContents());
            $this->updateBackpack($player, $items);
        });
        $inventory->send($player);
    }

    public function setBackpack(string $playerName, InvMenu $inventory): void
    {
        Await::f2c(function () use ($playerName, $inventory): Generator {
            $backpack = yield from MyGlobalExecutor::getInstance()->first((new BackpackModel)->where('username', $playerName));
            if ($backpack !== null) {
                $items = $this->decode($backpack->items);
                $inventory->getInventory()->setContents($items);
            } else {
                yield from MyGlobalExecutor::getInstance()->create(BackpackModel::class, [
                    "username" => $playerName,
                    "items" => "{}"
                ]);
            }
        });
    }

    public function updateBackpack(Player $player, string $items): void
    {
        Await::f2c(function () use ($player, $items) {
            $backpack = yield from MyGlobalExecutor::getInstance()->first((new BackpackModel)->where('username', $player->getName()));
            if ($items !== $backpack->items) {
                $backpack->update(['items' => $items]);
                $player->sendTip("§8» §aItems in your backpack have been saved!");
            }
        });
    }

    public function encode(array $array): string
    {
        return json_encode(
            array_map(
                fn($item) => $item->jsonSerialize(),
                $array
            )
        );
    }

    public function decode(string $items): array
    {
        return array_map(
            fn($itemData) => Item::jsonDeserialize($itemData),
            json_decode($items, true)
        );
    }
}