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
            ->field('network')->equals($network)
            ->field('channel')->equals($channel)
            ->getQuery()
            ->execute();
    }

    /**
     * @param $network
     * @param $channel
     * @return mixed
     */
    public function findAllByChannelAndDate($network, $channel, \MongoDate $from, \MongoDate $to) {
        return $this->createQueryBuilder()
            ->sort('date', 'ASC')
            ->field('network')->equals($network)
            ->field('channel')->equals($channel)
            ->field('date')->gte($from)
            ->field('date')->lt($to)
            ->getQuery()
            ->execute();
    }

    /**
     * @param $network
     * @param $channel
     * @return mixed
     */
    public function findAllByChannelAndType($network, $channel, $type) {
        return $this->createQueryBuilder()
            ->sort('date', 'ASC')
            ->field('network')->equals($network)
            ->field('channel')->equals($channel)
            ->field('type')->equals($type)
            ->getQuery()
            ->execute();
    }
}
