<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;

/**
 * Interface EligibleSpecialsReassignHandlerInterface
 */
interface EligibleSpecialsReassignHandlerInterface extends HandlerInterface
{
    /**
     * @param SpecialSubjectInterface $subject
     */
    public function handle(SpecialSubjectInterface $subject): void;
}
