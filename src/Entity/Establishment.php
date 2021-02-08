<?php

namespace App\Entity;

use App\Repository\EstablishmentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EstablishmentRepository::class)
 */
class Establishment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $FHRSID;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $localAuthorityBusinessID;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addressLine1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addressLine2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addressLine3;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addressLine4;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $postCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\ManyToOne(targetEntity=Rating::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $rating;

    /**
     * @ORM\Column(type="date")
     */
    private $ratingDate;

    /**
     * @ORM\ManyToOne(targetEntity=Authority::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $authority;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFHRSID(): ?int
    {
        return $this->FHRSID;
    }

    public function setFHRSID(int $FHRSID): self
    {
        $this->FHRSID = $FHRSID;

        return $this;
    }

    public function getLocalAuthorityBusinessID(): ?string
    {
        return $this->localAuthorityBusinessID;
    }

    public function setLocalAuthorityBusinessID(string $localAuthorityBusinessID): self
    {
        $this->localAuthorityBusinessID = $localAuthorityBusinessID;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function setAddressLine1(?string $addressLine1): self
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function setAddressLine2(?string $addressLine2): self
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    public function getAddressLine3(): ?string
    {
        return $this->addressLine3;
    }

    public function setAddressLine3(?string $addressLine3): self
    {
        $this->addressLine3 = $addressLine3;

        return $this;
    }

    public function getAddressLine4(): ?string
    {
        return $this->addressLine4;
    }

    public function setAddressLine4(?string $addressLine4): self
    {
        $this->addressLine4 = $addressLine4;

        return $this;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function setPostCode(?string $postCode): self
    {
        $this->postCode = $postCode;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getRating(): ?Rating
    {
        return $this->rating;
    }

    public function setRating(?Rating $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getRatingDate(): ?\DateTimeInterface
    {
        return $this->ratingDate;
    }

    public function setRatingDate(\DateTimeInterface $ratingDate): self
    {
        $this->ratingDate = $ratingDate;

        return $this;
    }

    public function getAuthority(): ?Authority
    {
        return $this->authority;
    }

    public function setAuthority(?Authority $authority): self
    {
        $this->authority = $authority;

        return $this;
    }
}
