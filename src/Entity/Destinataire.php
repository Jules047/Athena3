<?php

namespace App\Entity;

use App\Repository\DestinataireRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DestinataireRepository::class)]
#[ORM\Table(name: 'destinataire')]
class Destinataire
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    #[Groups(['default'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Message::class, inversedBy: 'destinataires')]
    #[ORM\JoinColumn(name: 'message_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Groups(['default'])]
    private ?Message $message = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'destinataires')]
    #[ORM\JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Groups(['default'])]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['default'])]
    private bool $lu = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['default'])]
    private ?\DateTimeInterface $dateLecture = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function isLu(): bool
    {
        return $this->lu;
    }

    public function setLu(bool $lu): self
    {
        $this->lu = $lu;
        return $this;
    }

    public function getDateLecture(): ?\DateTimeInterface
    {
        return $this->dateLecture;
    }

    public function setDateLecture(?\DateTimeInterface $dateLecture): self
    {
        $this->dateLecture = $dateLecture;
        return $this;
    }
}
