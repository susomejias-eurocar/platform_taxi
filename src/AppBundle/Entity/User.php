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
     * @ORM\ManyToOne(targetEntity="Permission", inversedBy="user")
     * @ORM\JoinColumn(name="permission_id", referencedColumnName="id")
     */
    private $permission;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="user")
     */
    private $companys;

    /**
     * @var int
     *
     * @ORM\OneToMany(targetEntity="Driver", mappedBy="userId")
     */
    private $drivers;
    
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

    public function getRoles()
    {
        return array('ROLE_USER');
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
     * Set permission
     *
     * @param \AppBundle\Entity\Permission $permission
     * @return User
     */
    public function setPermission(\AppBundle\Entity\Permission $permission = null)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Get permission
     *
     * @return \AppBundle\Entity\Permission 
     */
    public function getPermission()
    {
        return $this->permission;
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
}
