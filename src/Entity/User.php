<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;



/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="username",message="用户名已存在")
 * @UniqueEntity(fields="email",message="邮箱已存在")
 * @UniqueEntity(fields="phone",message="手机号已存在")
 * @ORM\HasLifecycleCallbacks() //使用生命周期
 */
class User implements UserInterface //注册用
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="用户名不能为空")
     */
    private $username;
    
    private $plainPassword;
    /**
     * @ORM\Column(type="array")
     */
    private $roles;
    /**
     * @ORM\Column(type="string", length=255)
     *  //@Assert\NotBlank(message="密码不能为空")
     * 
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="邮箱地址不能为空")
     * @Assert\Email(message="邮件地址不合法")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=11)
     * @Assert\NotBlank(message="手机号不能为空")
     */
    private $phone;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $age;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * 新增 start
     */
    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
        $this->status = true;
    }
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
    public function setPlainPasswrod($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }
    public function getRoles()
    {
        return $this->roles;
    }
    public function getSalt()
    {
        return '';
    }
    public function eraseCredentials()
    {

    }
    //上面是注册用
    //下面是生命周期处理
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->created_at = new \Datetime();
        $this->updated_at = new \Datetime();
    }
    /**
     * @ORM\PostPersist
     */
    public function setUpdatedAtValue()
    {
        $this->updated_at = new \Datetime();
    }
    /**
     * 新增 end
     */
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getGender(): ?bool
    {
        return $this->gender;
    }

    public function setGender(?bool $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): self
    {
        $this->age = $age;

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
}
