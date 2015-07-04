<?php

namespace Korobi\WebBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class ChatRepository extends DocumentRepository {

    /**
     * @param $network
     * @param $channel
     * @return mixed
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
     * @param $network
     * @param $channel
     * @param \MongoDate $from
     * @param \MongoDate $to
     * @return mixed
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
     * @param $network
     * @param $channel
     * @param \MongoId $from
     * @param \MongoDate $to
     * @return mixed
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
     * @param $network
     * @param $channel
     * @param $type
     * @return mixed
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
     * @param $network
     * @param $channel
     * @return mixed
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

    public function findLastMessages($count) {
        return $this->createQueryBuilder()
            ->sort('date', 'DESC')
            ->field('type')
                ->equals('MESSAGE')
            ->limit($count)
            ->getQuery()
            ->execute();
    }

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
