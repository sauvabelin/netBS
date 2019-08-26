<?php

namespace Ovesco\WhatsappBundle\Util;

class UserSlug
{
    public static function getSlug($username) {

        return sha1($username);
    }
}
