<?php

namespace App\Repositories;

use App\Models\PriceAction;
use Illuminate\Database\Eloquent\Collection;

class PriceActionsRepo
{
    protected PriceAction $model;

    /**
     * @param  PriceAction  $model
     */
    public function __construct(PriceAction $model)
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
            ->get();
    }

    /**
     * @param  int  $entryId
     * @param  int  $userId
     * @return PriceAction
     */
    public function getSingleEntry(int $entryId, int $userId): PriceAction
    {
        return $this->model
            ->newModelQuery()
            ->where('id', $entryId)
            ->where('user_id', $userId)
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
            ->delete();
    }

    /**
     * @return Collection
     */
    public function getAllActive(): Collection
    {
        return $this->model
            ->newModelQuery()
            ->where('active', 1)
            ->get();
    }
}
