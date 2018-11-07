<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

interface HandlerInterface
{
    /**
     * @param object $object
     */
    public function handle($object): void;
}
