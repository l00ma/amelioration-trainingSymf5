<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use \Symfony\Component\Validator\Constraints as Asserts;

class PropertySearch
{
    /**
     * @var string|null
     * @Asserts\Regex(pattern="/^[0-9]{5}$/", message="Code postal erroné")
     */
    private $codePostal;

    /**
     * @var int
     * @Asserts\Range(min=1) 
     */
    private $nbid;

    /**
     * @var int|null
     * @Asserts\Range(min=1000) 
     */
    private $maxPrice;

    /**
     * @var int|null
     * @Asserts\Range(min=10, max=400) 
     */
    private $minSurface;

    /**
     * @var int|null
     * @Asserts\Range(min=10, max=400) 
     */
    private $maxSurface;

    /**
     * @var int|null
     * @Asserts\Range(min=1, max=10) 
     */
    private $nbrRooms;

    /**
     * @var arrayCollection
     */
    private $options;

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getMaxPrice(): ?int
    {
        return $this->maxPrice;
    }

    /**
     * @param int|null $maxPrice
     * @return PropertySearch
     */
    public function setMaxPrice(int $maxPrice): PropertySearch
    {
        $this->maxPrice = $maxPrice;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getNbrRooms(): ?int
    {
        return $this->nbrRooms;
    }

    /**
     * @param int|null $nbrRooms
     * @return PropertySearch
     */
    public function setNbrRooms(int $nbrRooms): PropertySearch
    {
        $this->nbrRooms = $nbrRooms;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinSurface(): ?int
    {
        return $this->minSurface;
    }

    /**
     * @param int|null $minSurface
     * @return PropertySearch
     */
    public function setMinSurface(int $minSurface): PropertySearch
    {
        $this->minSurface = $minSurface;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxSurface(): ?int
    {
        return $this->maxSurface;
    }

    /**
     * @param int|null $maxSurface
     * @return PropertySearch
     */
    public function setMaxSurface(int $maxSurface): PropertySearch
    {
        $this->maxSurface = $maxSurface;
        return $this;
    }

    /**
     * @return arrayCollection 
     */
    public function getOptions(): ArrayCollection
    {
        return $this->options;
    }

    /**
     * @param arrayCollection $options
     */
    public function setOptions(ArrayCollection $options): void
    {
        $this->options = $options;
    }

    /**
     * @return string|null
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * @param string|null $codePostal
     * @return PropertySearch
     */
    public function setCodePostal($codePostal): PropertySearch
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * @return int
     */
    public function getNbid()
    {
        return $this->nbid;
    }

    /**
     * @param int|null $nbid
     * @return PropertySearch
     */
    public function setNbid($nbid): PropertySearch
    {
        $this->nbid = $nbid;

        return $this;
    }
}
