<?php

namespace App\Services\Orders;

use App\Services\LegacyJobService;

final class OrderItemService
{
    public function __construct(private readonly LegacyJobService $legacy)
    {
    }

    public function updateItemDetails(mixed ...$arguments): mixed
    {
        return $this->legacy->updateItemDetails(...$arguments);
    }

    public function updateItem(mixed ...$arguments): mixed
    {
        return $this->legacy->updateItem(...$arguments);
    }

    public function addItem(mixed ...$arguments): mixed
    {
        return $this->legacy->addItem(...$arguments);
    }

    public function removeItem(mixed ...$arguments): mixed
    {
        return $this->legacy->removeItem(...$arguments);
    }

    public function restoreItem(mixed ...$arguments): mixed
    {
        return $this->legacy->restoreItem(...$arguments);
    }


}
