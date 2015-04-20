<?php

namespace OpenStack\Common\Resource;

trait HasWaiterTrait
{
    public function waitUntil($status, $timeout = 60, $sleepPeriod = 1)
    {
        $startTime = time();

        while (true) {
            $this->retrieve();

            if ($this->status == $status || $this->shouldHalt($timeout, $startTime)) {
                break;
            }

            sleep($sleepPeriod);
        }
    }

    public function waitWithCallback(callable $fn, $timeout = 60, $sleepPeriod = 1)
    {
        $startTime = time();

        while (true) {
            $this->retrieve();

            $response = call_user_func_array($fn, [$this]);

            if ($response === true || $this->shouldHalt($timeout, $startTime)) {
                break;
            }

            sleep($sleepPeriod);
        }
    }

    private function shouldHalt($timeout, $startTime)
    {
        if ($timeout === false) {
            return false;
        }

        return time() - $startTime >= $timeout;
    }

    public function waitUntilActive($timeout = 60)
    {
        $this->waitUntil('ACTIVE');
    }
} 