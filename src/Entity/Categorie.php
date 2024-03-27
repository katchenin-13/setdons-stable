<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
#[UniqueEntity(['libelle'], message: 'Ce nom est déjà utilisé')]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Veuillez renseigner le nom de la nature de la communauté')]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "le nom de la nature de la communauté doit faire au moins {{ limit }} caractères",
        maxMessage: "le nom de la nature de la communauté ne doit pas faire plus de {{ limit }} caractères"
    )]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    #[Gedmo\Blameable(on: 'create')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Communaute::class,orphanRemoval: true, cascade:['persist'])]
    private Collection $communautes;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeInterface $UpdatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeInterface $CreatedAt = null;
    


 
    public function __construct()
    {
        $this->communautes = new ArrayCollection();
  
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

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

    /**
     * @return Collection<int, Communaute>
     */
    public function getCommunautes(): Collection
    {
        return $this->communautes;
    }

    public function addCommunaute(Communaute $communaute): self
    {
        if (!$this->communautes->contains($communaute)) {
            $this->communautes->add($communaute);
            $communaute->setCategorie($this);
        }

        return $this;
    }

    public function removeCommunaute(Communaute $communaute): self
    {
        if ($this->communautes->removeElement($communaute)) {
            // set the owning side to null (unless already changed)
            if ($communaute->getCategorie() === $this) {
                $communaute->setCategorie(null);
            }
        }

        return $this;
    }
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(\DateTimeInterface $CreatedAt): self
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->UpdatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $UpdatedAt): self
    {
        $this->UpdatedAt = $UpdatedAt;

        return $this;
    }


   
}
