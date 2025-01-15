<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class Notification extends Model
{
    use Prunable;


    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        $prunables = static::where('created_at', '<=', now()->subDays(10));

        return $prunables;
    }
}
