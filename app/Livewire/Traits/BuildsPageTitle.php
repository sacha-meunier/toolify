<?php

namespace App\Livewire\Traits;

/**
 * Builds page titles as "Page - Scope", omitting the scope when there isn't one.
 */
trait BuildsPageTitle
{
    protected function pageTitle(string $page, ?string $scope = null): string
    {
        return $scope ? "{$page} - {$scope}" : $page;
    }
}
