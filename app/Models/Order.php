<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Spatie\Translatable\HasTranslations;

class Order extends Model
{
    use HasTranslations, Prunable;

    protected $fillable = [
        'user_id', 'status', 'total_price'
    ];

    public $translatable = ['status'];

    protected $appends = ['translatedStatus'];

    protected $hidden = ['status'];

    public function getTranslatedStatusAttribute($val)
    {
        return $this->status;
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        $prunables = static::where('status->en', 'pending')->where('created_at', '<=', now()->subDays(3));

        $prunables1 = $prunables->get();
        if (!$prunables1->isEmpty())
            foreach ($prunables1 as $prunable)
                foreach ($prunable->items as $item) {
                    $item->product->increment('amount', $item->quantity);
                }

        return $prunables;
    }

    /**
     * Prepare the model for pruning.
     */
    protected function pruning(): void
    {
        // ...
    }
}
