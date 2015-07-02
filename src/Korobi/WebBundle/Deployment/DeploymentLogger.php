<?php

namespace Korobi\WebBundle\Deployment;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Kernel;

class DeploymentLogger {

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Kernel $kernel
     * @param LoggerInterface $logger
     */
    public function __construct(Kernel $kernel, LoggerInterface $logger) {
        $this->kernel = $kernel;
        $this->logger = $logger;
    }


    /**
     * Only log if debug is enabled.
     *
     * @param string $message The log message.
     * @param array $context The log context.
     * @param bool $error If this log entry is an error.
     */
    public function debug($message, array $context = [], $error = false) {
        if ($this->kernel->isDebug()) {
            if ($error) {
                $this->logger->error($message, $context);
            } else {
                $this->logger->info($message, $context);
            }
        }
    }
}
