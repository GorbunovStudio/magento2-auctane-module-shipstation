<?php

namespace Auctane\Api;

use Auctane\Api\Exception\AuthenticationFailedException;

/**
 * Interface CheckInterface
 */
interface CheckInterface
{
    /**
     * GET for GET api
     * @return bool
     * @throws AuthenticationFailedException
     */
    public function check(): bool;
}
