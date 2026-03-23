<?php

class WIToken
{

    public static function generate($length=32)
    {
        return bin2hex(random_bytes($length));
    }

}

?>