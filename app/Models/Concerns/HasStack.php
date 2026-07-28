<?php

namespace App\Models\Concerns;

use App\Models\Stack;
use App\Models\Tool;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasStack
{
    /**
     * This owner's stack, if it has one yet.
     */
    public function stack(): MorphOne
    {
        return $this->morphOne(Stack::class, 'owner');
    }

    /**
     * The owner's stack, created on first use — a Stack row only exists once something is added to it.
     */
    public function stackOrCreate(): Stack
    {
        if ($this->stack === null) {
            $this->setRelation('stack', $this->stack()->create());
        }

        return $this->stack;
    }

    public function hasInStack(Tool $tool): bool
    {
        return $this->stack?->tools()->whereKey($tool->id)->exists() ?? false;
    }
}
