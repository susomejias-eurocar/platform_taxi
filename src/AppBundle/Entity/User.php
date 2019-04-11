<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $last_name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20)
     */
    private $phone;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="users")
     */
    private $companys;

    /**
     * @var int
     *
     * @ORM\OneToMany(targetEntity="Driver", mappedBy="user")
     */
    private $drivers;

    /**
     * @var string
     *
     * @ORM\Column(name="tokenRegister", type="string", length=255, nullable=true)
     */
    private $tokenRegister;

    /**
     * @var string
     *
     * @ORM\Column(name="tokenPassword", type="string", length=255, nullable=true)
     */    
    private $tokenPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="expirationTokenRegister", type="string", nullable=true)
     */    
    private $expirationTokenRegister;

    /**
     * @var string
     *
     * @ORM\Column(name="expirationTokenPassword", type="string", nullable=true)
     */    
    private $expirationTokenPassword;

    /**
    * @ORM\Column(type="json_array")
    */
    private $roles = array();

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }
        /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }
 
    public function getSalt()
    {
        return null;
    }
 
    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->drivers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->expirationTokenRegister = date("d-m-Y",strtotime(date("d-m-Y")."+ 1 days")); 
        $this->tokenRegister = bin2hex(random_bytes(64));
        $this->active = 0;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return User
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }


    /**
     * Set id
     */
    public function setIdUser($id)
    {
        $this->id = $id;

        return $this;
    }
/**
     * Set companys
     *
     * @param \AppBundle\Entity\Company $companys
     * @return User
     */
    public function setId(\AppBundle\Entity\Company $companys = null)
    {
        $this->companys = $companys;

        return $this;
    }

    /**
     * Get companys
     *
     * @return \AppBundle\Entity\Company 
     */
    public function getCompanys()
    {
        return $this->companys;
    }

    /**
     * Add drivers
     *
     * @param \AppBundle\Entity\Driver $drivers
     * @return User
     */
    public function addDriver(\AppBundle\Entity\Driver $drivers)
    {
        $this->drivers[] = $drivers;

        return $this;
    }

    /**
     * Remove drivers
     *
     * @param \AppBundle\Entity\Driver $drivers
     */
    public function removeDriver(\AppBundle\Entity\Driver $drivers)
    {
        $this->drivers->removeElement($drivers);
    }

    /**
     * Get drivers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDrivers()
    {
        return $this->drivers;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set last_name
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;

        return $this;
    }

    /**
     * Get last_name
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set companys
     *
     * @param \AppBundle\Entity\Company $companys
     * @return User
     */
    public function setCompanys(\AppBundle\Entity\Company $companys = null)
    {
        $this->companys = $companys;

        return $this;
    }

    /**
     * Set tokenRegister
     *
     * @param string $tokenRegister
     * @return User
     */
    public function setTokenRegister($tokenRegister)
    {
        $this->tokenRegister = $tokenRegister;

        return $this;
    }

    /**
     * Get tokenRegister
     *
     * @return string 
     */
    public function getTokenRegister()
    {
        return $this->tokenRegister;
    }

    /**
     * Set tokenPassword
     *
     * @param string $tokenPassword
     * @return User
     */
    public function setTokenPassword($tokenPassword)
    {
        $this->tokenPassword = $tokenPassword;

        return $this;
    }

    /**
     * Get tokenPassword
     *
     * @return string 
     */
    public function getTokenPassword()
    {
        return $this->tokenPassword;
    }

    /**
     * Set expirationTokenRegister
     *
     * @param string $expirationTokenRegister
     * @return User
     */
    public function setExpirationTokenRegister($expirationTokenRegister)
    {
        $this->expirationTokenRegister = $expirationTokenRegister;

        return $this;
    }

    /**
     * Get expirationTokenRegister
     *
     * @return string 
     */
    public function getExpirationTokenRegister()
    {
        return $this->expirationTokenRegister;
    }

    /**
     * Set expirationTokenPassword
     *
     * @param string $expirationTokenPassword
     * @return User
     */
    public function setExpirationTokenPassword($expirationTokenPassword)
    {
        $this->expirationTokenPassword = $expirationTokenPassword;

        return $this;
    }

    /**
     * Get expirationTokenPassword
     *
     * @return string 
     */
    public function getExpirationTokenPassword()
    {
        return $this->expirationTokenPassword;
    }
}
