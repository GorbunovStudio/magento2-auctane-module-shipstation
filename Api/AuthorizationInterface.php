<?php
namespace Auctane\Api;

interface AuthorizationInterface
{
    /**
     * This method determines if a token is valid.
     *
     * @param string $token
     * @return bool
     */
    public function isAuthorized(string $token): bool;
}
