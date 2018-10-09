<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

/**
 * Class AbstractHandler
 */
abstract class AbstractHandler implements LoggableHandlerInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * AbstractHandler constructor.
     * @param LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * @param $message
     * @param string $level
     */
    protected function log($message, $level = LogLevel::NOTICE)
    {
        $this->logger->log(
            $level,
            $message
        );
    }
}
