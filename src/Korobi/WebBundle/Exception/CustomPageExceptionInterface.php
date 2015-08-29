<?php

namespace Korobi\WebBundle\Exception;

/**
 * All implementing classes will be displayed with their own error page.
 * This is more of a marker interface than anything else.
 * @package Korobi\WebBundle\Exception
 */
interface CustomPageExceptionInterface {

    /**
     * @return string Error page name used to display error details to user.
     */
    public function getViewName();
}
