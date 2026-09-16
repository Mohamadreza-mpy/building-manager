<?php

namespace App\Services;

use App\Models\Building;

class ReportService
{
    public function summary(Building $building): array
    {
        $chargeQuery = $building->charges();

        return ['building_id' => $building->id, 'apartments_count' => $building->apartments()->count(), 'occupied_apartments_count' => $building->apartments()->whereNotNull('resident_id')->count(), 'charges_total' => (string) (clone $chargeQuery)->sum('amount'), 'paid_charges_total' => (string) (clone $chargeQuery)->where('status', 'paid')->sum('amount'), 'outstanding_charges_total' => (string) (clone $chargeQuery)->whereIn('status', ['pending', 'overdue'])->sum('amount'), 'expenses_total' => (string) $building->expenses()->sum('amount')];
    }
}
