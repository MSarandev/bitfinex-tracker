<?php

namespace App\Services;

use App\Helpers\AuthCheck;
use App\Models\PriceAction;
use App\Repositories\PriceActionsRepo;
use Illuminate\Database\Eloquent\Collection;

class PriceActionService
{
    protected PriceActionsRepo $repo;

    public function __construct(PriceActionsRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param  array  $validated
     * @return void
     */
    public function addNewPriceAction(array $validated): void
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
     * @return PriceAction
     */
    public function getSinglePriceAction(int $entryId): PriceAction
    {
        $checkedUser = AuthCheck::checkUser();

        return $this->repo->getSingleEntry($entryId, $checkedUser->getAuthIdentifier());
    }

    /**
     * @param  int  $entryId
     * @return void
     */
    public function activatePriceAction(int $entryId): void
    {
        $checkedUser = AuthCheck::checkUser();

        $this->repo->activateAnEntry($entryId, $checkedUser->getAuthIdentifier());
    }

    /**
     * @param  int  $entryId
     * @return void
     */
    public function deactivatePriceAction(int $entryId): void
    {
        $checkedUser = AuthCheck::checkUser();

        $this->repo->deactivateAnEntry($entryId, $checkedUser->getAuthIdentifier());
    }

    /**
     * @param  int  $entryId
     * @return void
     */
    public function deletePriceAction(int $entryId): void
    {
        $checkedUser = AuthCheck::checkUser();

        $this->repo->deleteEntry($entryId, $checkedUser->getAuthIdentifier());
    }
}
