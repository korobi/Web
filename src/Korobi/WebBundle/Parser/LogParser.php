<?php

namespace Korobi\WebBundle\Parser;

use Korobi\WebBundle\Document\Chat;
use Korobi\WebBundle\IrcLogs\RenderSettings;
use Symfony\Component\Translation\TranslatorInterface;

class LogParser implements LogParserInterface {

    /**
     * @var TranslatorInterface
     */
    private $t;

    /**
     * @param TranslatorInterface $t
     */
    public function __construct(TranslatorInterface $t) {
        $this->t = $t;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    // @Kashike flails
    public function parseAction(Chat $chat) {
        $result = '';

        $result .= $this->getSpanForColour(
            $this->getColourForActor($chat),
            $this->transformActor($chat->getActorName(), $chat->getActorPrefix())
        );
        $result .= ' ';
        $result .= IRCTextParser::parse($chat->getMessage());

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    // @Kashike joined the channel
    public function parseJoin(Chat $chat) {
        $actor = $this->transformActor($chat->getActorName(), $chat->getActorPrefix());
        $actorHostname = IRCTextParser::createHostnameTag($chat->getActorHostname());
        $result = $this->t->trans('irc.joined_channel', ['%actor%' => $actor, '%actor_hostname%' => $actorHostname]);

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    // @lol768 was kicked by @Kashike (hello)
    public function parseKick(Chat $chat) {
        $recipientActor = $this->transformActor($chat->getRecipientName(), $chat->getRecipientPrefix());
        $actor = $this->transformActor($chat->getActorName(), $chat->getActorPrefix());
        $kickMessage = $chat->getMessage();
        $result = $this->t->trans('irc.was_kicked_by', [
            '%recipient_actor%' => $recipientActor,
            '%actor%' => $actor,
            '%kick_message%' => $kickMessage,
        ]);

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    // meow!
    public function parseMessage(Chat $chat) {
        return IRCTextParser::parse($chat->getMessage());
    }

    /**
     * @param Chat $chat
     * @return string
     */
    // Server sets mode +CQnst
    // @Kashike sets mode +b *!*@test.com
    public function parseMode(Chat $chat) {
        $actor = $this->transformActor($chat->getActorName(), $chat->getActorPrefix());
        $mode = $chat->getMessage();

        // I see this format as part of IRC< hence why not in i18n
        if ($chat->getRecipientPrefix() !== null) {
            $mode .= $this->transformUserModeToLetter($chat->getRecipientPrefix());
            $mode .= ' ';
            $mode .= $this->transformActor($chat->getRecipientName());
        } else if ($chat->getChannelMode() !== null) {
            $mode .= $this->transformChannelModeToLetter($chat->getChannelMode());
            $mode .= ' ';
            $mode .= $this->transformActor($chat->getRecipientHostname());
        }

        $result = $this->t->trans('irc.set_mode', ['%actor%' => $actor, '%mode%' => $mode]);

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    // [00:00:00] ** @drtshock is now known as @Trent
    public function parseNick(Chat $chat) {
        $originalActor = $this->transformActor($chat->getActorName(), $chat->getActorPrefix());
        $newActor = $this->transformActor($chat->getRecipientName(), $chat->getActorPrefix());
        $result = $this->t->trans('irc.known_as', ['%original_actor%' => $originalActor, '%new_actor%' => $newActor]);

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public function parsePart(Chat $chat) {
        $actor = $this->transformActor($chat->getActorName(), $chat->getActorPrefix());
        $actorHostname = IRCTextParser::createHostnameTag($chat->getActorHostname());
        $result = $this->t->trans('irc.left_channel', ['%actor%' => $actor, '%actor_hostname%' => $actorHostname]);

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public function parseQuit(Chat $chat) {
        $actor = $this->transformActor($chat->getActorName(), $chat->getActorPrefix());
        $actorHostname = IRCTextParser::createHostnameTag($chat->getActorHostname());
        $result = $this->t->trans('irc.has_quit', ['%actor%' => $actor, '%actor_hostname%' => $actorHostname]);

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public function parseTopic(Chat $chat) {
        $topic = IRCTextParser::parse($chat->getMessage());
        if ($chat->getActorName() === Chat::ACTOR_INTERNAL) {
            $result = $this->t->trans('irc.topic_is', ['%topic%' => $topic]);
        } else {
            $actor = $this->transformActor($chat->getActorName(), $chat->getActorPrefix());
            $result = ' ' . $this->t->trans('irc.has_changed_topic_to', ['%topic%' => $topic, '%actor%' => $actor]);
        }

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public function getColourForActor(Chat $chat) {
        switch ($chat->getType()) {
            case 'ACTION':
            case 'MESSAGE':
                return NickColours::getColourForNick($this->transformActor($chat->getActorName()));
            case 'PART':
            case 'QUIT':
                return '04';
            case 'JOIN':
                return '03';
            default:
                return RenderSettings::ACTION_SERVER_COLOUR;
        }
    }

    /**
     * Returns the name to display for that chat entry.
     *
     * @param Chat $chat
     * @return string
     */
    public function getDisplayName(Chat $chat) {
        switch ($chat->getType()) {
            case 'MESSAGE':
                return $chat->getActorName();
            case 'ACTION':
                return RenderSettings::ACTION_USER_PREFIX;
            case 'JOIN':
                return RenderSettings::JOIN_USER_PREFIX;
            case 'PART':
            case 'QUIT':
                return RenderSettings::PART_USER_PREFIX;
            default:
                return RenderSettings::ACTION_SERVER_PREFIX;
        }
    }

    /**
     * Returns the nickname of the actor for that chat entry or its hostname
     * in case there is no nickname.
     *
     * @param Chat $chat
     * @return string
     */
    public function getActorName(Chat $chat) {
        return $chat->getActorName() == Chat::ACTOR_INTERNAL
            ? $this->transformActor($chat->getActorHostname())
            : $chat->getActorName();
    }

    // -----------------
    // ---- Helpers ----
    // -----------------

    /**
     * @param $colour
     * @param $text
     * @return string
     */
    private function getSpanForColour($colour, $text) {
        return IRCTextParser::createColorTag($colour, IRCTextParser::DEFAULT_BACKGROUND)
            . $text . IRCTextParser::closeTag();
    }

    /**
     * Transform an actor name.
     *
     * @param $actor
     * @param $prefix
     * @return string
     */
    public function transformActor($actor, $prefix = '') {
        if ($actor == Chat::ACTOR_INTERNAL) {
            return $this->t->trans('irc.server');
        }

        if(empty($prefix) || $prefix == 'NORMAL') {
            return $actor;
        }

        return '<span class="' . strtolower($prefix) . '">' . $actor . '</span>';
    }

    /**
     * @param $mode
     * @return string
     */
    private function transformChannelModeToLetter($mode) {
        switch ($mode) {
            case 'BAN':
                return 'b';
            case 'QUIET':
                return 'q';
            case 'NORMAL':
            default:
                return '';
        }
    }

    /**
     * @param $mode
     * @return string
     */
    private function transformUserModeToLetter($mode) {
        switch ($mode) {
            case 'OWNER':
                return 'q';
            case 'ADMIN':
                return 'a';
            case 'OPERATOR':
                return 'o';
            case 'HALF_OP':
                return 'h';
            case 'VOICE':
                return 'v';
            case 'NORMAL':
            default:
                return '';
        }
    }
}
