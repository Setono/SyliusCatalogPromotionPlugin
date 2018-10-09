<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Controller;

use FOS\RestBundle\View\View;
use Setono\SyliusBulkSpecialsPlugin\Handler\HandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController as BaseResourceController;
use Sylius\Component\Resource\Exception\UpdateHandlingException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class ResourceController extends BaseResourceController
{
    protected function bulkAction(HandlerInterface $handler, $bulkActionId, $actionId, Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, $bulkActionId);
        $resources = $this->resourcesCollectionProvider->get($configuration, $this->repository);

        if (
            $configuration->isCsrfProtectionEnabled() &&
            !$this->isCsrfTokenValid($bulkActionId, $request->request->get('_csrf_token'))
        ) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        if (!count($resources)) {
            $this->flashHelper->addErrorFlash($configuration, 'choose_bulk_action_items');

            return $this->redirectHandler->redirectToReferer($configuration);
        }

        $this->eventDispatcher->dispatchMultiple($bulkActionId, $configuration, $resources);

        foreach ($resources as $resource) {
            $event = $this->eventDispatcher->dispatchPreEvent($actionId, $configuration, $resource);

            if ($event->isStopped() && !$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }
            if ($event->isStopped()) {
                $this->flashHelper->addFlashFromEvent($configuration, $event);

                if ($event->hasResponse()) {
                    return $event->getResponse();
                }

                return $this->redirectHandler->redirectToIndex($configuration, $resource);
            }

            try {
                $handler->handle($resource);
            } catch (UpdateHandlingException $exception) {
                if (!$configuration->isHtmlRequest()) {
                    return $this->viewHandler->handle(
                        $configuration,
                        View::create(null, $exception->getApiResponseCode())
                    );
                }

                $this->flashHelper->addErrorFlash($configuration, $exception->getFlash());

                return $this->redirectHandler->redirectToReferer($configuration);
            }

            $postEvent = $this->eventDispatcher->dispatchPostEvent($actionId, $configuration, $resource);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create(null, Response::HTTP_NO_CONTENT));
        }

        $this->flashHelper->addSuccessFlash($configuration, $bulkActionId);

        if (isset($postEvent) && $postEvent->hasResponse()) {
            return $postEvent->getResponse();
        }

        return $this->redirectHandler->redirectToIndex($configuration);
    }
}
