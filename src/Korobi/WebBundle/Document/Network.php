<?php

namespace Korobi\WebBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="networks",repositoryClass="Korobi\WebBundle\Repository\NetworkRepository")
 */
class Network {

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    private $slug;

    /**
     * @MongoDB\String
     */
    private $name;

    /**
     * @MongoDB\String
     */
    private $description_short;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get slug
     *
     * @return string $slug
     */
    public function getSlug() {
        return $this->slug;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return self
     */
    public function setSlug($slug) {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Get descriptionShort
     *
     * @return string $descriptionShort
     */
    public function getDescriptionShort() {
        return $this->description_short;
    }

    /**
     * Set descriptionShort
     *
     * @param string $descriptionShort
     * @return self
     */
    public function setDescriptionShort($descriptionShort) {
        $this->description_short = $descriptionShort;
        return $this;
    }
}
