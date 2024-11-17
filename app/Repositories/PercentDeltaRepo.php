<?php

namespace App\Repositories;

use App\Models\PercentDelta;
use Illuminate\Database\Eloquent\Collection;

class PercentDeltaRepo
{
    protected PercentDelta $model;

    /**
     * @param  PercentDelta  $model
     */
    public function __construct(PercentDelta $model)
    {
        $this->model = $model;
    }

    /**
     * @param  int  $userId
     * @return Collection
     */
    public function getAllEntriesPerUser(int $userId): Collection
    {
        return $this->model
            ->newModelQuery()
            ->where('user_id', $userId)
            ->where('deleted_at', null)
            ->get();
    }

    /**
     * @param  int  $entryId
     * @param  int  $userId
     * @return PercentDelta
     */
    public function getSingleEntry(int $entryId, int $userId): PercentDelta
    {
        return $this->model
            ->newModelQuery()
            ->where('id', $entryId)
            ->where('user_id', $userId)
            ->where('deleted_at', null)
            ->firstOrFail();
    }

    /**
     * @param  array  $validated
     * @return void
     */
    public function createNewEntry(array $validated): void
    {
        $this->model
            ->newModelQuery()
            ->create($validated);
    }

    /**
     * @param  int  $entryId
     * @param  int  $userId
     * @return void
     */
    public function activateAnEntry(int $entryId, int $userId): void
    {
        $entry = $this->model
            ->newModelQuery()
            ->where('id', $entryId)
            ->where('user_id', $userId)
            ->where('deleted_at', null)
            ->firstOrFail();

        if ($entry->active === 0) {
            $entry->active = 1;

            $entry->save();
        }
    }

    /**
     * @param  int  $entryId
     * @param  int  $userId
     * @return void
     */
    public function deactivateAnEntry(int $entryId, int $userId): void
    {
        $entry = $this->model
            ->newModelQuery()
            ->where('id', $entryId)
            ->where('user_id', $userId)
            ->where('deleted_at', null)
            ->firstOrFail();

        if ($entry->active === 1) {
            $entry->active = 0;

            $entry->save();
        }
    }

    /**
     * @param  int  $entryId
     * @param  int  $userId
     * @return void
     */
    public function deleteEntry(int $entryId, int $userId): void
    {
        $this->model
            ->newModelQuery()
            ->where('id', $entryId)
            ->where('user_id', $userId)
            ->update(['deleted_at' => now()]);
    }

    /**
     * @return Collection
     */
    public function getAllActive(): Collection
    {
        return $this->model
            ->newModelQuery()
            ->where('active', 1)
            ->where('deleted_at', null)
            ->get();
    }

    /**
     * @return Collection
     */
    public static function getAllStatic(): Collection
    {
        return PercentDelta::where('active', 1)
            ->where('deleted_at', null)
            ->get();
    }
}
