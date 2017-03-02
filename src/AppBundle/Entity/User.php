<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ORM\Table(name="users",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="users_nickname_unique", columns={"nickname"})}
 * )
 **/
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     **/
    protected $id;

    /**
     * @ORM\Column(type="string")
     **/
    protected $nickname;

    /**
     * @ORM\Column(type="string")
     **/
    protected $password;

    /**
     * @ORM\Column(type="string")
     **/
    protected $mail;

    protected $plainPassword;

    /**
     * @ORM\Column(type="string")
     **/
    protected $roles;

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function getRoles()
    {
        return explode(',', $this->roles);
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->nickname;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getNickname()
    {
        return $this->nickname;
    }

    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }

    public function getMail()
    {
        return $this->mail;
    }
    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }
}
?>
