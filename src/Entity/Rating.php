<?php

namespace App\Entity;

use App\Repository\RatingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RatingRepository::class)
 */
class Rating
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ratingKey;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ratingKeyName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRatingKey(): ?string
    {
        return $this->ratingKey;
    }

    public function setRatingKey(string $ratingKey): self
    {
        $this->ratingKey = $ratingKey;

        return $this;
    }

    public function getRatingKeyName(): ?string
    {
        return $this->ratingKeyName;
    }

    public function setRatingKeyName(string $ratingKeyName): self
    {
        $this->ratingKeyName = $ratingKeyName;

        return $this;
    }
}
