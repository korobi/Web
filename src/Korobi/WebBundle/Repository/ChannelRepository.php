<?php

namespace Korobi\WebBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class ChannelRepository extends DocumentRepository {

    /**
     * @param $network
     * @return mixed
     */
    public function findAllByNetwork($network) {
        return $this->createQueryBuilder()
            ->sort('name', 'ASC')
            ->field('network')->equals($network)
            ->getQuery()
            ->execute();
    }

    /**
     * @param $network
     * @param $channel
     * @return mixed
     */
    public function findByChannel($network, $channel) {
        return $this->createQueryBuilder()
            ->sort('name', 'ASC')
            ->field('network')->equals($network)
            ->field('channel')->equals($channel)
            ->getQuery()
            ->execute();
    }
}
