<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string response_xml
 * @property int status
 * @property string created_at
 * @property string updated_at
 */
class Currency extends Model
{
    const STATUS_ERROR = 0;
    const STATUS_SUCCESS = 1;

    protected $table = 'currency';

    public function scopeToday(Builder $query)
    {
        $query->where('created_at', '>=', Carbon::now()->startOfDay())->where('created_at', '<=', Carbon::now()->endOfDay());
    }
}
