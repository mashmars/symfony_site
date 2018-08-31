<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Security\Core\User\UserInterface;
/**
 * @ORM\Entity(repositoryClass="App\Repository\AdminRepository")
 * @ORM\HasLifecycleCallbacks() 
 */
class Admin implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $descript;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $roles;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_login_time;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $last_login_ip;
    /**
     * @ORM\PrePersist
     * 生命周期插入前
     */
    public function setInitValue()
    {
        $this->created_at = new \Datetime();
        $this->updated_at = new \Datetime();
        $this->roles = ['ROLE_ADMIN'];
        $this->status = 1;
    }
    public function getId()
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

    public function getDescript(): ?string
    {
        return $this->descript;
    }

    public function setDescript(?string $descript): self
    {
        $this->descript = $descript;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    //userinterface
    public function getSalt()
    {
        return null;
    }
    public function eraseCredentials()
    {

    }
    //userinterface

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getLastLoginTime(): ?\DateTimeInterface
    {
        return $this->last_login_time;
    }

    public function setLastLoginTime(?\DateTimeInterface $last_login_time): self
    {
        $this->last_login_time = $last_login_time;

        return $this;
    }

    public function getLastLoginIp(): ?string
    {
        return $this->last_login_ip;
    }

    public function setLastLoginIp(?string $last_login_ip): self
    {
        $this->last_login_ip = $last_login_ip;

        return $this;
    }
}
