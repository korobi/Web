<?php

namespace Korobi\WebBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class ChatRepository extends DocumentRepository {

    /**
     * @param string $network
     * @param string $channel
     * @return \Doctrine\MongoDB\Cursor
     */
    public function findAllByChannel($network, $channel) {
        return $this->createQueryBuilder()
            ->sort('date', 'ASC')
            ->field('network')
                ->equals($network)
            ->field('channel')
                ->equals($channel)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $network
     * @param string $channel
     * @param \MongoDate $from
     * @param \MongoDate $to
     * @return \Doctrine\MongoDB\Cursor
     */
    public function findAllByChannelAndDate($network, $channel, \MongoDate $from, \MongoDate $to) {
        return $this->createQueryBuilder()
            ->sort('date', 'ASC')
            ->field('network')
                ->equals($network)
            ->field('channel')
                ->equals($channel)
            ->field('date')
                ->gte($from)
            ->field('date')
                ->lt($to)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $network
     * @param string $channel
     * @param \MongoId $from
     * @param \MongoDate $to
     * @return \Doctrine\MongoDB\Cursor
     */
    public function findAllByChannelAndId($network, $channel, \MongoId $from, \MongoDate $to) {
        return $this->createQueryBuilder()
            ->sort('date', 'ASC')
            ->field('network')
                ->equals($network)
            ->field('channel')
                ->equals($channel)
            ->field('_id')
                ->gt($from)
            ->field('date')
                ->lt($to)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $network
     * @param string $channel
     * @param string $type
     * @return \Doctrine\MongoDB\Cursor
     */
    public function findAllByChannelAndType($network, $channel, $type) {
        return $this->createQueryBuilder()
            ->sort('date', 'ASC')
            ->field('network')
                ->equals($network)
            ->field('channel')
                ->equals($channel)
            ->field('type')
                ->equals($type)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $network
     * @param string $channel
     * @return \Doctrine\MongoDB\Cursor
     */
    public function findFirstByChannel($network, $channel) {
        return $this->createQueryBuilder()
            ->sort('date', 'ASC')
            ->field('network')
                ->equals($network)
            ->field('channel')
                ->equals($channel)
            ->limit(1)
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $count
     * @return \Doctrine\MongoDB\Cursor
     */
    public function findLastMessages($count) {
        return $this->createQueryBuilder()
            ->sort('date', 'DESC')
            ->field('type')
                ->equals('MESSAGE')
            ->limit($count)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $network
     * @param string $channel
     * @param int $count
     * @return \Doctrine\MongoDB\Cursor
     */
    public function findLastChatsByChannel($network, $channel, $count) {
        return $this->createQueryBuilder()
            ->sort('date', 'DESC')
            ->field('network')
                ->equals($network)
            ->field('channel')
                ->equals($channel)
            ->field('type')
                ->equals('MESSAGE')
            ->limit($count)
            ->getQuery()
            ->execute();
    }

}
