<?php

namespace App\Entity;

use App\Repository\RsvpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RsvpRepository::class)]
class Rsvp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column]
    private ?bool $isAttending = null;

    #[ORM\Column(type: Types::GUID)]
    private ?string $uuid = null;

    /**
     * @var Collection<int, People>
     */
    #[ORM\OneToMany(targetEntity: People::class, mappedBy: 'rsvp')]
    private Collection $people;

    #[ORM\Column(length: 255)]
    private ?string $ip_address = null;

    public function __construct()
    {
        $this->people = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function isAttending(): ?bool
    {
        return $this->isAttending;
    }

    public function setIsAttending(bool $isAttending): static
    {
        $this->isAttending = $isAttending;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return Collection<int, People>
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    public function addPerson(People $person): static
    {
        if (!$this->people->contains($person)) {
            $this->people->add($person);
            $person->setRsvp($this);
        }

        return $this;
    }

    public function removePerson(People $person): static
    {
        if ($this->people->removeElement($person)) {
            // set the owning side to null (unless already changed)
            if ($person->getRsvp() === $this) {
                $person->setRsvp(null);
            }
        }

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    public function setIpAddress(string $ip_address): static
    {
        $this->ip_address = $ip_address;

        return $this;
    }
}
