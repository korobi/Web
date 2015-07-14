<?php

namespace Korobi\WebBundle\IrcLogs;

/**
 * Provides common global settings for @see RenderManager.
 * @package Korobi\WebBundle\IrcLogs
 */
class RenderSettings {

    const MAX_NICK_LENGTH = 10;
    const JOIN_USER_PREFIX = '-->';
    const PART_USER_PREFIX = '<--';
    const ACTION_USER_PREFIX = '*';
    const ACTION_SERVER_PREFIX = '--';
    const ACTION_SERVER_COLOUR = '14';
}
