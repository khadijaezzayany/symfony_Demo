<?php

namespace App\Application\Sonata\UserBundle\Entity;

use App\Entity\Centre;
use App\Entity\DemandeContact;
use App\Entity\Devis;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;

/**
 * This file has been generated by the SonataEasyExtendsBundle.
 *
 * @link https://sonata-project.org/easy-extends
 *
 * References:
 * @link http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en
 */
class User extends BaseUser
{
    /**
     * @var int $id
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $civilite;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $societe;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $siret;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $code_postal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pays;

    /**
     * @ORM\Column(type="datetime", nullable=true, )
     */
    private $derniere_connexion;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $type_client = false;

    /**
     * @ORM\ManyToMany(targetEntity=Centre::class, mappedBy="user")
     */
    private $centre;

    /**
     * @ORM\OneToMany(targetEntity=DemandeContact::class, mappedBy="user")
     */
    private $demande_contacts;

    /**
     * @ORM\OneToMany(targetEntity=Devis::class, mappedBy="client")
     */
    private $devis_client;

    /**
     * @ORM\OneToMany(targetEntity=Devis::class, mappedBy="commercial")
     */
    private $devis_commercial;

    /**
     * @ORM\Column(name="googleUid", type="string", length=255, nullable=true)
     */
    protected $googleUid;

    /**
     * @ORM\Column(name="google_access_token", type="string", length=255, nullable=true)
     */
    protected $google_access_token;
    
    public function __construct()
    {
        $this->centre = new ArrayCollection();
        $this->demande_contacts = new ArrayCollection();
        $this->devis = new ArrayCollection();
        $this->devis_commercial = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    public function getCivilite(): ?string
    {
        return $this->civilite;
    }

    public function setCivilite(?string $civilite): self
    {
        $this->civilite = $civilite;

        return $this;
    }

    public function getSociete(): ?string
    {
        return $this->societe;
    }

    public function setSociete(?string $societe): self
    {
        $this->societe = $societe;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(?string $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getDerniereConnexion(): ?\DateTimeInterface
    {
        return $this->derniere_connexion;
    }

    public function setDerniereConnexion(?\DateTimeInterface $derniere_connexion): self
    {
        $this->derniere_connexion = $derniere_connexion;

        return $this;
    }
    

    public function getTypeClient(): ?bool
    {
        return $this->type_client;
    }

    public function setTypeClient(?bool $type_client): self
    {
        $this->type_client = $type_client;

        return $this;
    }

    
    public function getGoogleUid(): ?string
    {
        return $this->googleUid;
    }

    public function setGoogleUid(?string $googleUid): self
    {
        $this->googleUid = $googleUid;

        return $this;
    }
    
    /**
     * @return Collection|Centre[]
     */
    public function getCentre(): Collection
    {
        return $this->centre;
    }

    public function addCentre(Centre $centre): self
    {
        if (!$this->centre->contains($centre)) {
            $this->centre[] = $centre;
            $centre->addUser($this);
        }

        return $this;
    }

    public function removeCentre(Centre $centre): self
    {
        if ($this->centre->contains($centre)) {
            $this->centre->removeElement($centre);
            $centre->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|DemandeContact[]
     */
    public function getDemandeContacts(): Collection
    {
        return $this->demande_contacts;
    }

    public function addDemandeContact(DemandeContact $demandeContact): self
    {
        if (!$this->demande_contacts->contains($demandeContact)) {
            $this->demande_contacts[] = $demandeContact;
            $demandeContact->setUser($this);
        }

        return $this;
    }

    public function removeDemandeContact(DemandeContact $demandeContact): self
    {
        if ($this->demande_contacts->removeElement($demandeContact)) {
            // set the owning side to null (unless already changed)
            if ($demandeContact->getUser() === $this) {
                $demandeContact->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Devis[]
     */
    public function getDevisClients(): Collection
    {
        return $this->devis_client;
    }

    public function addDevisClient(Devis $devi_client): self
    {
        if (!$this->devis_client->contains($devi_client)) {
            $this->devis_client[] = $devi_client;
            $devi_client->setClient($this);
        }

        return $this;
    }

    public function removeDevisClient(Devis $devi_client): self
    {
        if ($this->devis_client->removeElement($devi_client)) {
            // set the owning side to null (unless already changed)
            if ($devi_client->getClient() === $this) {
                $devi_client->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Devis[]
     */
    public function getDevisCommercial(): Collection
    {
        return $this->devis_commercial;
    }

    public function addDevisCommercial(Devis $devisCommercial): self
    {
        if (!$this->devis_commercial->contains($devisCommercial)) {
            $this->devis_commercial[] = $devisCommercial;
            $devisCommercial->setCommercial($this);
        }

        return $this;
    }

    public function removeDevisCommercial(Devis $devisCommercial): self
    {
        if ($this->devis_commercial->removeElement($devisCommercial)) {
            // set the owning side to null (unless already changed)
            if ($devisCommercial->getCommercial() === $this) {
                $devisCommercial->setCommercial(null);
            }
        }

        return $this;
    }
}