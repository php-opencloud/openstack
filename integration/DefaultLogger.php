<?php

namespace integration\OpenStack;

use Psr\Log\AbstractLogger;

class DefaultLogger extends AbstractLogger
{
    public function log($level, $message, array $context = [])
    {
        fwrite('php://stdout', $this->format($level, $message, $context));
    }

    private function format($level, $message, $context)
    {
        $msg = strtr($message, $context);

        return sprintf("%s: %s\n", strtoupper($level), $msg);
    }
}