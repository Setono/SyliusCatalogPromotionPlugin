<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Controller;

use Setono\SyliusBulkSpecialsPlugin\SetonoSyliusBulkSpecialsActions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SpecialController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function bulkRecalculateAction(Request $request): Response
    {
        return $this->bulkAction(
            $this->container->get('setono_sylius_bulk_specials.recalculate_handler.special'),
            SetonoSyliusBulkSpecialsActions::BULK_RECALCULATE_SPECIAL,
            SetonoSyliusBulkSpecialsActions::RECALCULATE_SPECIAL,
            $request
        );
    }
}
