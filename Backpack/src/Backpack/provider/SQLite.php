<?php

namespace Backpack\provider;

use Backpack\BACKPACK;
use pocketmine\item\Item;
use pocketmine\player\Player;
use SQLite3;

class SQLite
{
    /**
     * @var SQLite
     */
    private static SQLite $database;

    /**
     * @var SQLite3
     */
    private SQLite3 $sqlite;

    public function __construct()
    {
        self::$database = $this;
        $this->sqlite = new SQLite3(BACKPACK::getInstance()->getDataFolder() . "backpacks.db");
        $this->sqlite->exec("CREATE TABLE IF NOT EXISTS Backpacks(playerName VARCHAR(20), items TEXT)");
    }

    /**
     * @param Player $player
     * @return bool|array
     */
    public function decode(Player $player): bool|array
    {
        foreach ($this->getBackpackData() as $datum) if ($datum["playerName"] == $player->getName()) return array_map(fn($i) => Item::jsonDeserialize($i), json_decode($datum["items"], true));
        return true;
    }

    /**
     * @param array $array
     * @return bool|string
     */
    public function encode(array $array): bool|string
    {
        return json_encode(array_map(fn($i) => $i->jsonSerialize(), $array));
    }

    /**
     * @param string $playerName
     * @return bool
     */
    public function isDataControl(string $playerName): bool
    {
        $data = $this->sqlite->prepare("SELECT * FROM Backpacks WHERE playerName = :playerName");
        $data->bindValue(":playerName", $playerName);
        $control = $data->execute();
        if (empty($control->fetchArray(SQLITE3_ASSOC))) return false; else {
            return true;
        }
    }

    /**
     * @param string $playerName
     * @param string $items
     * @return void
     */
    public function createBackpackData(string $playerName, string $items): void
    {
        $data = $this->sqlite->prepare("INSERT INTO Backpacks(playerName, items) VALUES(:playerName, :items)");
        $data->bindValue(":playerName", $playerName);
        $data->bindValue(":items", $items);
        $data->execute();
    }

    /**
     * @param string $playerName
     * @param string $items
     * @return void
     */
    public function updateBackpackData(string $playerName, string $items): void
    {
        $data = $this->sqlite->prepare("UPDATE Backpacks SET items = :items WHERE playerName = :playerName");
        $data->bindValue(":items", $items);
        $data->bindValue(":playerName", $playerName);
        $data->execute();
    }

    /**
     * @return array
     */
    public function getBackpackData(): array
    {
        $data = $this->sqlite->prepare("SELECT * FROM Backpacks");
        $control = $data->execute();
        $array = [];

        while ($rows = $control->fetchArray(SQLITE3_ASSOC)) {
            $array[] = $rows;
        }
        return $array;
    }

    /**
     * @return SQLite
     */
    public static function getDatabase(): SQLite
    {
        return self::$database;
    }
}
