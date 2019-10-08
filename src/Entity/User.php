<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

//15.----------------------Login: Contrainte unicité ds Entity user------------------
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
//j'ajoute en plus des contraintes de validation (voir : http://symfony.com/doc/current/reference/constraints.html)
use Symfony\Component\Validator\Constraints as Assert;
//15.----------------------Creer dossier Login ds src/ et SuccessHandler.php----------------------------------------------


//05.--------Login: on ajoute "implements UserInterface, \Serializable"--------------
/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="username", message="Ce pseudo existe déja !!")
 * @UniqueEntity(fields="email", message="Email déja inscrit")
 */
class User implements UserInterface, \Serializable 
//05.---------------------------------------------------------------------------
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min= 4, minMessage="Votre mot de passe doit faire minimum 4 caractéres")
     */
    private $password;

    /**
    *  @Assert\EqualTo(propertyPath="password", message="Vous n'avez pas tapé le même mot de passe")
    */
    public $confirm_password;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Recette", mappedBy="auteur")
     */
    private $recettes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cle;

    public function __construct()
    {
        $this->recettes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    
    
    //07.---------------Login: Modifier public function getRole-----------------
    
    // ATTENTION, IL FAUT RENVOYER UN TABLEAU...
    // A CAUSE DU UserInterface
    
    //public function getRole(): ?string
    public function getRole()
    {
        return  $this->role;
    }

    public function getRoles()
    {
        return  [ $this->role ];
    }


    //07.------Login :Se rendre ensuite dans Controller/UserController.php------
    
    
    

    public function setRole(?string $role): self
    {
        $this->role = $role;
        return $this;
    }
    
    
    
    
    //06.------------Login: On ajoute les methode suplementaire-----------------
    
    // METHODES SUPPLEMENTAIRES POUR IMPLEMENTER UserInterface
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function eraseCredentials()
    {
        
    }
    
    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }
    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized, array('allowed_classes' => false));
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|Recette[]
     */
    public function getRecettes(): Collection
    {
        return $this->recettes;
    }

    public function addRecette(Recette $recette): self
    {
        if (!$this->recettes->contains($recette)) {
            $this->recettes[] = $recette;
            $recette->setAuteur($this);
        }

        return $this;
    }

    public function removeRecette(Recette $recette): self
    {
        if ($this->recettes->contains($recette)) {
            $this->recettes->removeElement($recette);
            // set the owning side to null (unless already changed)
            if ($recette->getAuteur() === $this) {
                $recette->setAuteur(null);
            }
        }

        return $this;
    }

    public function getCle(): ?string
    {
        return $this->cle;
    }

    public function setCle(?string $cle): self
    {
        $this->cle = $cle;

        return $this;
    }

    //06.-----------------------------------------------------------------------




    
    
}
