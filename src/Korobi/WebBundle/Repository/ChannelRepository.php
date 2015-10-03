<?php

namespace Korobi\WebBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class ChannelRepository extends DocumentRepository {

    /**
     * @param string $network
     * @return \Doctrine\MongoDB\Cursor
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
     * @param string $network
     * @param string $channel
     * @return \Doctrine\MongoDB\Cursor
     */
    public function findByChannel($network, $channel) {
        return $this->createQueryBuilder()
            ->sort('channel', 'ASC')
            ->field('network')
                ->equals($network)
            ->field('channel')
                ->equals(new \MongoRegex('/^' . preg_quote($channel, '/') . '/i'))
            ->getQuery()
            ->execute();
    }

    /**
     * Returns an array containing every network containing at least 1 channel and
     * the number of channels it contains.
     *
     * @param bool $private whether the query should contain private channels or not
     * @return array
     */
    public function countChannelsByNetwork($private = false) {
        $query = [
            [
                '$group' => [
                    '_id' => ['network' => '$network'],
                    'count' => ['$sum' => 1],
                ],
            ], [
                '$project' => [
                    '_id' => 0,
                    'network' => '$_id.network',
                    'count' => '$count',
                ],
            ], [
                '$match' => ['count' => ['$ne' => 0]],
            ],
        ];
        if(!$private) {
            array_unshift($query, [
                '$match' => ['key' => null],
            ]);
        }

        return $this
            ->getDocumentManager()
            ->getDocumentCollection('KorobiWebBundle:Channel')
            ->aggregate($query)
            ->toArray();
    }

    /**
     * Looks for messages, actions etc (valid content only).
     *
     * @param int $limit Number of channels to grab.
     * @return \Doctrine\MongoDB\Cursor The public n last channels.
     */
    public function getRecentlyActiveChannels($limit) {
        return $this->createQueryBuilder()
            ->sort('last_activity_valid', 'DESC') // TODO
            ->field('key')
                ->equals(null)
            ->limit($limit)
            ->getQuery()
            ->execute();
    }

    /**
     * @return \Doctrine\MongoDB\Cursor
     */
    public function findPublicChannels() {
        return $this->createQueryBuilder()
            ->field('key')
            ->equals(null)
            ->getQuery()
            ->execute();
    }

}
