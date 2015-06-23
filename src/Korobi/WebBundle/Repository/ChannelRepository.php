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
            ->sort('channel', 'ASC')
            ->field('network')
                ->equals($network)
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
            ->sort('channel', 'ASC')
            ->field('network')
                ->equals($network)
            ->field('channel')
                ->equals(new \MongoRegex('/^' . $channel . '/i'))
            ->getQuery()
            ->execute();
    }

    /**
     * Looks for messages, actions etc (valid content only).
     *
     * @param int $limit Number of channels to grab.
     * @return array The public n last channels.
     */
    public function getRecentlyActiveChannels($limit) {
        return $this->createQueryBuilder()
            ->sort('last_valid_content_at', 'DESC')
            ->field('key')
                ->equals(null)
            ->limit($limit)
            ->getQuery()
            ->execute();
    }
}
