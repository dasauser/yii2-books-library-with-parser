<?php

namespace common\helpers;

class NameHelper
{

    /**
     * Returns  in lower case.
     *
     * @param string $name name.
     *
     * @return string name in lower case.
     */
    public static function toLowerCase(string $name): string
    {
        return mb_strtolower($name);
    }

    /**
     * Returns name with removed spaces.
     *
     * @param string $name name.
     *
     * @return string name without spaces.
     */
    public static function removeSpaces(string $name): string
    {
        return str_replace(" ", "", $name);
    }
}