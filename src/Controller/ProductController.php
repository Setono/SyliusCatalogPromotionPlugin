<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Controller;

use Setono\SyliusBulkSpecialsPlugin\SetonoSyliusBulkSpecialsActions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends ResourceController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function bulkReassignAction(Request $request): Response
    {
        return $this->bulkAction(
            $this->container->get('setono_sylius_bulk_specials.handler.eligible_specials_reassign'),
            SetonoSyliusBulkSpecialsActions::BULK_REASSIGN_PRODUCT,
            SetonoSyliusBulkSpecialsActions::REASSIGN_PRODUCT,
            $request
        );
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function bulkRecalculateAction(Request $request): Response
    {
        return $this->bulkAction(
            $this->container->get('setono_sylius_bulk_specials.recalculate_handler.product'),
            SetonoSyliusBulkSpecialsActions::BULK_RECALCULATE_PRODUCT,
            SetonoSyliusBulkSpecialsActions::RECALCULATE_PRODUCT,
            $request
        );
    }
}