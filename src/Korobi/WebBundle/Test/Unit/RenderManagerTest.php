<?php

namespace Korobi\WebBundle\Test\Unit;

use DateTime;
use Korobi\WebBundle\Document\Chat;
use Korobi\WebBundle\IrcLogs\RenderManager;
use Korobi\WebBundle\IrcLogs\RenderSettings;
use Korobi\WebBundle\Parser\LogParserInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RenderManagerTest extends WebTestCase {

    public function testRenderingIllegalNotice() {
        $sut = new RenderManager(new ParserStub());
        $illegalNotice = new Chat();
        $illegalNotice->setType('MESSAGE');
        $illegalNotice->setNotice(true);
        $illegalNotice->setNoticeTarget('VOICE');
        $this->assertEmpty($sut->renderLogs([$illegalNotice]));
    }

    public function testRenderingMultipleDocuments() {
        $sut = new RenderManager(new ParserStub());
        $chat1 = new Chat();
        $chat1->setType('KICK');
        $chat1->setDate(new DateTime());
        $chat2 = new Chat();
        $chat2->setDate(new DateTime());
        $chat2->setType('MESSAGE');
        $this->assertCount(2, $sut->renderLogs([$chat1, $chat2]));
    }

    public function testActualDataReturned() {
        $sut = new RenderManager(new ParserStub());
        $chat1 = new Chat();
        $chat1->setType('EXOTIC');
        $chat1->setDate(new DateTime());
        $out = $sut->renderLogs([$chat1]);
        $forTwig = $out[0];
        $this->assertCount(1, $out);
        $this->assertArrayHasKeys([
            'id', 'timestamp', 'type', 'role', 'nickColour',
            'displayNick', 'realNick', 'nickTooLong', 'nick', 'message',
        ], $forTwig);
        $this->assertEquals($forTwig['type'], 'exotic');
        $this->assertEquals($forTwig['message'], 'A message');
    }

    public function testRenderingWhitelist() {
        $sut = new RenderManager(new ParserStub());
        $chat1 = new Chat();
        $chat1->setType('KICK');
        $chat1->setDate(new DateTime());
        $chat2 = new Chat();
        $chat2->setDate(new DateTime());
        $chat2->setType('MESSAGE');
        $this->assertCount(1, $sut->renderLogs([$chat1, $chat2], ['message']));
    }

    public function testNickChopping() {
        $sut = new RenderManager(new ParserStub());
        $chat1 = new Chat();
        $chat1->setType('EXOTIC');
        $name = str_repeat('a', RenderSettings::MAX_NICK_LENGTH + 5);
        $chat1->setActorName($name);
        $chat1->setDate(new DateTime());
        $out = $sut->renderLogs([$chat1]);
        $forTwig = $out[0];
        $this->assertEquals($forTwig['realNick'], $name);
        $this->assertNotEquals($forTwig['displayNick'], $name);
        $this->assertTrue($forTwig['nickTooLong']);
    }

    /**
     * @expectedException \Korobi\WebBundle\Exception\UnsupportedOperationException
     */
    public function testParsingUnparsableType() {
        $sut = new RenderManager(new ParserStub());
        $chat1 = new Chat();
        $chat1->setType('CAT');
        $chat1->setDate(new DateTime());
        $sut->renderLogs([$chat1]);
    }

    private function assertArrayHasKeys(array $keys, array $arr) {
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $arr);
        }
    }
}

class ParserStub implements LogParserInterface {

    /**
     * @param Chat $chat
     * @return string
     */
    public function parseExotic(Chat $chat) {
        return 'A message';
    }

    /**
     * Returns the name to display for that chat entry.
     *
     * @param Chat $chat
     * @return string
     */
    public function getDisplayName(Chat $chat) {
        return $this->getActorName($chat);
    }

    /**
     * Returns the nickname of the actor for that chat entry or its hostname
     * in case there is no nickname.
     *
     * @param Chat $chat
     * @return string
     */
    public function getActorName(Chat $chat) {
        return $chat->getActorName();
    }

    // --------------------------------------- //

    /**
     * @param Chat $chat
     * @return string
     */
    public function parseAction(Chat $chat) {
        // TODO: Implement parseAction() method.
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public function parseJoin(Chat $chat) {
        // TODO: Implement parseJoin() method.
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public function parseKick(Chat $chat) {
        // TODO: Implement parseKick() method.
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public function parseMessage(Chat $chat) {
        // TODO: Implement parseMessage() method.
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public function parseMode(Chat $chat) {
        // TODO: Implement parseMode() method.
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public function parseNick(Chat $chat) {
        // TODO: Implement parseNick() method.
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public function parsePart(Chat $chat) {
        // TODO: Implement parsePart() method.
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public function parseQuit(Chat $chat) {
        // TODO: Implement parseQuit() method.
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public function parseTopic(Chat $chat) {
        // TODO: Implement parseTopic() method.
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public function getColourForActor(Chat $chat) {
        // TODO: Implement getColourForActor() method.
    }

    /**
     * Transform an actor name.
     *
     * @param $actor
     * @param $prefix
     * @return string
     */
    public function transformActor($actor, $prefix = '') {
        // TODO: Implement transformActor() method.
    }
}
