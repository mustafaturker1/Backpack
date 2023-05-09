<?php

declare(strict_types=1);

namespace Backpack\executors;

use Backpack\MyDatabaseHandler;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use function array_shift;

class CustomExecutorThread extends MyDatabaseHandler
{

    public function handle(Connection $connection, array $data): mixed
    {
        $action = array_shift($data);
        if ($action === "createTables") {
            $builder = $connection->getSchemaBuilder();
            if ($builder->hasTable("backpacks")) {
                return null;
            }
            $builder->create("backpacks", function (Blueprint $table) {
                $table->string("username", 20);
                $table->text("items");
                $table->primary("username");
                $table->timestamps();
            });
        }

        return null;
    }
}