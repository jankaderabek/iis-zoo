<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class SpeciesCertificate extends Certificate
{
    /**
     * @var Species
     * @ORM\ManyToOne(targetEntity="Species")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $species;

    /**
     * @return Species
     */
    public function getSpecies()
    {
        return $this->species;
    }

    /**
     * @param Species $species
     */
    public function setSpecies(Species $species)
    {
        $this->species = $species;
    }
}
