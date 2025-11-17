<?php

namespace App\Entity;

use App\Entity\Vente;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\SessionRepository;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['session:read']],
    denormalizationContext: ['groups' => ['session:write']],
    shortName: 'Sessions',
    operations:[
        new GetCollection(
            uriTemplate:'/sessions',
            order: ['id' => 'DESC'],
            openapi: new Operation(
                summary: 'Récupérer les sessions',
                description:  'Récupérer les sessions',
            )
            
        ),
      new Post(
            uriTemplate: '/sessions',
            openapi: new Operation(
                summary: 'Ajouter une session',
                description: 'Ajouter une session'
            )
        ),
        new Get(
            uriTemplate: '/sessions/{id}',
            openapi: new Operation(
                description: 'Récupérer une session par ID',
                summary: 'Récupérer une session par ID'
            )
        ),

        new Patch(
            uriTemplate: '/sessions/{id}',
            openapi: new Operation(
                description: 'Modifier une session par ID',
                summary: 'Modifier une session par ID'
            )
        ),
        new Delete(
            uriTemplate: '/sessions/{id}',
            openapi: new Operation(
                description: 'Supprimer une session par ID',
                summary: 'Supprimer une session par ID'
            )
        ),
    ]
)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['session:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['session:read', 'session:write'])]
    private ?\DateTimeInterface $dateOuverture = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['session:read', 'session:write'])]
    private ?\DateTimeInterface $dateFermeture = null;

    #[ORM\Column(type: 'float')]
    #[Groups(['session:read', 'session:write'])]
    private float $montantInitial = 0.0;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['session:read', 'session:write'])]
    private ?float $montantFinal = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[Groups(['session:read', 'session:write'])]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: Vente::class)]
    #[Groups(['session:read'])]
    private Collection $ventes;

    public function __construct()
    {
        $this->ventes = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateOuverture(): ?\DateTimeInterface
    {
        return $this->dateOuverture;
    }
    public function setDateOuverture(\DateTimeInterface $dt): self
    {
        $this->dateOuverture = $dt;
        return $this;
    }

    public function getDateFermeture(): ?\DateTimeInterface
    {
        return $this->dateFermeture;
    }
    public function setDateFermeture(?\DateTimeInterface $dt): self
    {
        $this->dateFermeture = $dt;
        return $this;
    }

    public function getMontantInitial(): float
    {
        return $this->montantInitial;
    }
    public function setMontantInitial(float $m): self
    {
        $this->montantInitial = $m;
        return $this;
    }

    public function getMontantFinal(): ?float
    {
        return $this->montantFinal;
    }
    public function setMontantFinal(?float $m): self
    {
        $this->montantFinal = $m;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /** @return Collection|Vente[] */
    public function getVentes(): Collection
    {
        return $this->ventes;
    }
    public function addVente(Vente $vente): self
    {
        if (!$this->ventes->contains($vente)) {
            $this->ventes->add($vente);
            $vente->setSession($this);
        }
        return $this;
    }
    public function removeVente(Vente $vente): self
    {
        if ($this->ventes->removeElement($vente)) {
            if ($vente->getSession() === $this) {
                $vente->setSession(null);
            }
        }
        return $this;
    }
}
