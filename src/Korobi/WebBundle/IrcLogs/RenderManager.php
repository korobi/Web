<?php

namespace Korobi\WebBundle\IrcLogs;

use Korobi\WebBundle\Document\Chat;
use Korobi\WebBundle\Exception\UnsupportedOperationException;
use Korobi\WebBundle\Parser\LogParserInterface;

/**
 * Class RenderManager
 *
 * Try to provide a common class for controllers which
 * want to render logs on a page.
 *
 * @package Korobi\WebBundle\IrcLogs
 */
class RenderManager {

    /**
     * @var LogParserInterface
     */
    private $logParser;

    /**
     * RenderManager constructor.
     * @param LogParserInterface $logParser
     */
    public function __construct(LogParserInterface $logParser) {
        $this->logParser = $logParser;
    }

    /**
     * Returns an array of data arrays ready to be rendered by a twig macro.
     *
     * @param Chat[] $chats
     * @param array $typeWhitelist Optional array of types to allow. Lowercase please!
     * @return array Array of data after applying RenderManager::processChatDocument
     * @see processChatDocument
     */
    public function renderLogs(array $chats, array $typeWhitelist = []) {
        $out = [];
        $emptyWhitelist = count($typeWhitelist) == 0;

        foreach ($chats as $chat) {
            if ($chat->getNotice() && $chat->getNoticeTarget() !== 'NORMAL') { // Don't show vnotices/opnotices!
                continue;
            }

            if ($emptyWhitelist || in_array(strtolower($chat->getType()), $typeWhitelist)) {
                $out[] = $this->processChatDocument($chat);
            }
        }
        return $out;
    }

    /**
     * Gives an array of data for one chat document.
     *
     * @param Chat $chat A single chat document.
     * @return array The array of data for the twig macro.
     */
    public function processChatDocument($chat) {
        $nick = $this->logParser->getDisplayName($chat);
        return [
            'id'          => $chat->getId(), // MongoID of chat document
            'timestamp'   => $chat->getDate()->setTimezone(new \DateTimeZone('UTC')),
            'type'        => strtolower($chat->getType()), // Lowercase type identifier (e.g. 'join' or 'action')
            'role'        => $chat->getType() == 'MESSAGE' ? strtolower($chat->getActorPrefix()) : '', // @, + etc
            'nickColour'  => $this->logParser->getColourForActor($chat), // Colour of nick (applies weechat algorithm)
            'displayNick' => substr($nick, 0, RenderSettings::MAX_NICK_LENGTH + 1), // Chopped nick
            'realNick'    => $nick, // Full nick
            'nickTooLong' => strlen($nick) - RenderSettings::MAX_NICK_LENGTH > 1, // If the nick got chopped
            'nick'        => $this->logParser->getActorName($chat), // TODO: Better naming - returns hostname/nick
            'message'     => $this->getHtmlFragmentForChatMessage($chat), // HTML'd message
        ];

    }

    /**
     * @param Chat $chat The chat entry to pass off to the parser.
     * @return string The HTML fragment for the given chat message.
     * @throws UnsupportedOperationException If you try and parse an unsupported message type.
     */
    public function getHtmlFragmentForChatMessage(Chat $chat) {
        $method = 'parse' . ucfirst(strtolower($chat->getType())); // produces e.g. "parseJoin"
        try {
            $method = $this->getReflectionLogParser()->getMethod($method);
            return $method->invokeArgs($this->logParser, [$chat]);
        } catch (\ReflectionException $ex) {
            // we can't parse this type of message
            throw new UnsupportedOperationException("The method $method caused a reflection exception: " . $ex->getMessage());
        }
    }

    /**
     * @return \ReflectionClass The log parser reflection class.
     */
    private function getReflectionLogParser() {
        return new \ReflectionClass($this->logParser);
    }
}
