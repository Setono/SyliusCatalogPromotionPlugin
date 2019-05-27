<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Psr\Log\LoggerInterface;

abstract class AbstractHandler
{
    /**
     * @var LoggerInterface|null
     */
    protected $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    protected function log(string $message): void
    {
        if ($this->logger instanceof LoggerInterface) {
            $this->logger->info($message);
        }
    }
}
