<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Permission
 *
 * @ORM\Table(name="permission")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PermissionRepository")
 */
class Permission
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
     * @var int
     *
     * @ORM\Column(name="edit", type="integer")
     */
    private $edit;

    /**
     * @var int
     *
     * @ORM\Column(name="new", type="integer")
     */
    private $new;

    /**
     * @var int
     *
     * @ORM\Column(name="remove", type="integer")
     */
    private $remove;

    /**
     * @var int
     *
     * @ORM\OneToMany(targetEntity="User", mappedBy="permission")
     */
    private $user;

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
     * Set name
     *
     * @param string $name
     * @return Permission
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
     * Set edit
     *
     * @param integer $edit
     * @return Permission
     */
    public function setEdit($edit)
    {
        $this->edit = $edit;

        return $this;
    }

    /**
     * Get edit
     *
     * @return integer 
     */
    public function getEdit()
    {
        return $this->edit;
    }

    /**
     * Set new
     *
     * @param integer $new
     * @return Permission
     */
    public function setNew($new)
    {
        $this->new = $new;

        return $this;
    }

    /**
     * Get new
     *
     * @return integer 
     */
    public function getNew()
    {
        return $this->new;
    }

    /**
     * Set remove
     *
     * @param integer $remove
     * @return Permission
     */
    public function setRemove($remove)
    {
        $this->remove = $remove;

        return $this;
    }

    /**
     * Get remove
     *
     * @return integer 
     */
    public function getRemove()
    {
        return $this->remove;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add user
     *
     * @param \AppBundle\Entity\User $user
     * @return Permission
     */
    public function addUser(\AppBundle\Entity\User $user)
    {
        $this->user[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \AppBundle\Entity\User $user
     */
    public function removeUser(\AppBundle\Entity\User $user)
    {
        $this->user->removeElement($user);
    }

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUser()
    {
        return $this->user;
    }
}
