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
     * @ORM\Column(name="email", type="string", length=255)
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
     * @var int
     *
     * @ORM\Column(name="phone", type="integer")
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
     * @ORM\OneToMany(targetEntity="Company", mappedBy="user")
     */
    private $companys;

    /**
     * @var int
     *
     * @ORM\OneToMany(targetEntity="Driver", mappedBy="userId")
     */
    private $drivers;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->companys = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param integer $phone
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
     * @return integer 
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
     * Add companys
     *
     * @param \AppBundle\Entity\Company $companys
     * @return User
     */
    public function addCompany(\AppBundle\Entity\Company $companys)
    {
        $this->companys[] = $companys;

        return $this;
    }

    /**
     * Remove companys
     *
     * @param \AppBundle\Entity\Company $companys
     */
    public function removeCompany(\AppBundle\Entity\Company $companys)
    {
        $this->companys->removeElement($companys);
    }

    /**
     * Get companys
     *
     * @return \Doctrine\Common\Collections\Collection 
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
}
