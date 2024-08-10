<?php

namespace App\Entity;

use App\Repository\RapportJournalierRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RapportJournalierRepository::class)]
#[ORM\Table(name: 'rapportjournalier')]
class RapportJournalier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['default'])]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    #[Groups(['default'])]
    private \DateTimeInterface $date;

    #[ORM\ManyToOne(targetEntity: Collaborateur::class)]
    #[ORM\JoinColumn(name: 'collaborateur_id', referencedColumnName: 'id')]
    #[Groups(['default'])]
    private ?Collaborateur $collaborateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getCollaborateur(): ?Collaborateur
    {
        return $this->collaborateur;
    }

    public function setCollaborateur(?Collaborateur $collaborateur): self
    {
        $this->collaborateur = $collaborateur;
        return $this;
    }
}
