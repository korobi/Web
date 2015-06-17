<?php

namespace Korobi\WebBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class StatisticRepository extends DocumentRepository {

    /**
     * @param $network
     * @param $channel
     * @return mixed
     */
    public function findByChannel($network, $channel) {
        return $this->createQueryBuilder()
            ->sort('channel', 'ASC')
            ->field('network')
                ->equals($network)
            ->field('channel')
                ->equals(new \MongoRegex('/^' . $channel . '/i'))
            ->getQuery()
            ->execute();
    }
}
