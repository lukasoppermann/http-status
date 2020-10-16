<?php

namespace Lukasoppermann\Httpstatus;

interface LanguageInterface
{
    /**
     * Returns HTTP Status code.
     *
     * @return array
     */
    public function getHttpStatus();
}
