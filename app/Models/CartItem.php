<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class CartItem extends Model
{
    use Prunable;

    protected $fillable = [
        'cart_id', 'product_id', 'quantity'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        $prunables = static::where('created_at', '<=', now()->subHours(5));

        $prunables1 = $prunables->get();

        if (!$prunables1->isEmpty())
            foreach ($prunables1 as $prunable)
                $prunable->product->increment('amount', $prunable->quantity);

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
