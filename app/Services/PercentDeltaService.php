<?php

namespace App\Services;

use App\Helpers\AuthCheck;
use App\Models\PercentDelta;
use App\Repositories\PercentDeltaRepo;
use Illuminate\Database\Eloquent\Collection;

class PercentDeltaService
{
    protected PercentDeltaRepo $repo;

    public function __construct(PercentDeltaRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param  array  $validated
     * @return void
     */
    public function addNewPercentDelta(array $validated): void
    {
        $checkedUser = AuthCheck::checkUser();

        $validated['user_id'] = $checkedUser->getAuthIdentifier();

        $this->repo->createNewEntry($validated);
    }

    /**
     * @return Collection
     */
    public function getAllPerUser(): Collection
    {
        $checkedUser = AuthCheck::checkUser();

        return $this->repo->getAllEntriesPerUser($checkedUser->getAuthIdentifier());
    }

    /**
     * @param  int  $entryId
     * @return PercentDelta
     */
    public function getSinglePercentDelta(int $entryId): PercentDelta
    {
        $checkedUser = AuthCheck::checkUser();

        return $this->repo->getSingleEntry($entryId, $checkedUser->getAuthIdentifier());
    }

    /**
     * @param  int  $entryId
     * @return void
     */
    public function activatePercentDelta(int $entryId): void
    {
        $checkedUser = AuthCheck::checkUser();

        $this->repo->activateAnEntry($entryId, $checkedUser->getAuthIdentifier());
    }

    /**
     * @param  int  $entryId
     * @return void
     */
    public function deactivatePercentDelta(int $entryId): void
    {
        $checkedUser = AuthCheck::checkUser();

        $this->repo->deactivateAnEntry($entryId, $checkedUser->getAuthIdentifier());
    }

    /**
     * @param  int  $entryId
     * @return void
     */
    public function deletePercentDelta(int $entryId): void
    {
        $checkedUser = AuthCheck::checkUser();

        $this->repo->deleteEntry($entryId, $checkedUser->getAuthIdentifier());
    }
}
