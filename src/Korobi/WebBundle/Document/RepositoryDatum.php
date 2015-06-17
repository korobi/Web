<?php

namespace Korobi\WebBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="repositories",repositoryClass="Korobi\WebBundle\Repository\RepositoryRepository")
 */
class RepositoryDatum {

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    private $key;

    /**
     * @MongoDB\String
     */
    private $organisation;

    /**
     * @MongoDB\String
     */
    private $repository;

    /**
     * @MongoDB\String
     */
    private $display_tag;

    /**
     * @MongoDB\Collection
     */
    private $slugs;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get key
     *
     * @return string $key
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * Set key
     *
     * @param string $key
     * @return self
     */
    public function setKey($key) {
        $this->key = $key;
        return $this;
    }

    /**
     * Get organisation
     *
     * @return string $organisation
     */
    public function getOrganisation() {
        return $this->organisation;
    }

    /**
     * Set organisation
     *
     * @param string $organisation
     * @return self
     */
    public function setOrganisation($organisation) {
        $this->organisation = $organisation;
        return $this;
    }

    /**
     * Get repository
     *
     * @return string $repository
     */
    public function getRepository() {
        return $this->repository;
    }

    /**
     * Set repository
     *
     * @param string $repository
     * @return self
     */
    public function setRepository($repository) {
        $this->repository = $repository;
        return $this;
    }

    /**
     * Get displayTag
     *
     * @return string $displayTag
     */
    public function getDisplayTag() {
        return $this->display_tag;
    }

    /**
     * Set displayTag
     *
     * @param string $displayTag
     * @return self
     */
    public function setDisplayTag($displayTag) {
        $this->display_tag = $displayTag;
        return $this;
    }

    /**
     * Get slugs
     *
     * @return collection $slugs
     */
    public function getSlugs() {
        return $this->slugs;
    }

    /**
     * Set slugs
     *
     * @param collection $slugs
     * @return self
     */
    public function setSlugs($slugs) {
        $this->slugs = $slugs;
        return $this;
    }
}
