<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin;

final class SetonoSyliusBulkSpecialsActions
{
    public const RECALCULATE_SPECIAL = 'recalculate_special';

    public const BULK_RECALCULATE_SPECIAL = 'bulk_recalculate_special';

    public const RECALCULATE_PRODUCT = 'recalculate_product';

    public const BULK_RECALCULATE_PRODUCT = 'bulk_recalculate_product';

    public const REASSIGN_PRODUCT = 'reassign_product';

    public const BULK_REASSIGN_PRODUCT = 'bulk_reassign_product';

    /**
     * Prevent instantiating
     */
    private function __construct()
    {
    }
}
