<?php

namespace NetBS\SecureBundle\Firewall;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator as BaseAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;

class NetBSJWTAuthenticator extends BaseAuthenticator
{
    protected function getTokenExtractor()
    {
        return new AuthorizationHeaderTokenExtractor("Bearer", "x-authorization");
    }
}