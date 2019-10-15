<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Message\Handler;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

abstract class AbstractHandler implements LoggerAwareInterface, MessageHandlerInterface
{
    use LoggerAwareTrait;

    public function __construct()
    {
        $this->logger = new NullLogger();
    }
}
