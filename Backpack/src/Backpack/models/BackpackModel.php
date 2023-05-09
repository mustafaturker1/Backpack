<?php

declare(strict_types=1);

namespace Backpack\models;

use Backpack\BACKPACK;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 */
class BackpackModel extends Model
{
    protected $table = "backpacks";
    protected $primaryKey = "username";
    public $incrementing = false;
    public $timestamps = true;
    protected $connection = BACKPACK::CONN_NAME;

    protected $fillable = [
        "username",
        "items"
    ];
}
