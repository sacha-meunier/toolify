<?php

namespace App\Models\Concerns;

use App\Models\Survey;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasSurveys
{
    /**
     * The surveys this owner has saved.
     */
    public function surveys(): MorphMany
    {
        return $this->morphMany(Survey::class, 'owner');
    }
}
