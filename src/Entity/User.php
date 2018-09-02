<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="username",message="用户名已存在")
 * @UniqueEntity(fields="email",message="邮箱已存在")
 * @UniqueEntity(fields="phone",message="手机号已存在")
 * @UniqueEntity(fields="domain",message="该域名已使用")
 * @ORM\HasLifecycleCallbacks() //使用生命周期
 */
class User implements UserInterface , \Serializable //注册用 , \Serializable 登录用
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
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $headimg;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $domain;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Category", mappedBy="userid", orphanRemoval=true)
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tag", mappedBy="userid", orphanRemoval=true)
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="userid", orphanRemoval=true)
     */
    private $posts;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resume;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="userid")
     */
    private $comments;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $count_post;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $count_follower;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $count_like;
    /**
     * 注册新增 start
     */
    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
        $this->status = true;
        $this->isActive = true;
        $this->categories = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();        
    }
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }
    public function getRoles()
    {
        return ['ROLE_USER'];//$this->roles;
    }
    public function getSalt()
    {
        return null;
    }
    public function eraseCredentials()
    {

    }
    //上面是注册用
    //下面是生命周期处理
    /**
     * @ORM\PrePersist
     */
    public function setInitValue()
    {
        $this->created_at = new \Datetime();
        $this->updated_at = new \Datetime();
        //设置默认昵称
        $this->nickname = '小飞侠';
        $this->count_post = 0;
        $this->count_follower = 0;
        $this->count_like = 0;
        $this->createDomain();
    }
    /**
     * @ORM\PreUpdate
     */
    public function setUpdateValue()
    {
        $this->updated_at = new \Datetime();
        //设置默认昵称
        if(!$this->nickname){
            $this->nickname = '小飞侠';
        }
    }
    public function createDomain()
    {
        $regex = '/^[ a-z0-9]$/i';        
        if(preg_match($regex, $this->username)){
            //符合条件的情况，如果处理不符合的情况，在Else里面进行处理;
            $this->domain = $this->username ;
        }else{
            $domain = '';
            $str = 'abcdefghijklmnopqrstuvwxyz0123456789';
            for($i= 1; $i<= 6; ++$i) {
                $domain.=$str[mt_rand(0, 35)];
            }
            $this->domain = $domain;
        }
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

    /**
     * 登录 新增 start
     */
    /*
    public function getIsActive()
    {
        return $this->isActive;
    }
    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }*/
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
            $this->email,
            $this->phone,
            $this->isActive,
            $this->roles,
        ]);
    }
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            $this->email,
            $this->phone,
            $this->isActive,
            $this->roles,
        ) = unserialize($serialized, array('allowed_classes' => false));
    }

    public function getHeadimg(): ?string
    {
        return $this->headimg;
    }

    public function setHeadimg(?string $headimg): self
    {
        $this->headimg = $headimg;

        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(?string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setUserid($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            // set the owning side to null (unless already changed)
            if ($category->getUserid() === $this) {
                $category->setUserid(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->setUserid($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
            // set the owning side to null (unless already changed)
            if ($tag->getUserid() === $this) {
                $tag->setUserid(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUserid($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUserid() === $this) {
                $post->setUserid(null);
            }
        }

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(?string $resume): self
    {
        $this->resume = $resume;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUserid($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUserid() === $this) {
                $comment->setUserid(null);
            }
        }

        return $this;
    }

    public function getCountPost(): ?int
    {
        return $this->count_post;
    }

    public function setCountPost(?int $count_post): self
    {
        $this->count_post = $count_post;

        return $this;
    }

    public function getCountFollower(): ?int
    {
        return $this->count_follower;
    }

    public function setCountFollower(?int $count_follower): self
    {
        $this->count_follower = $count_follower;

        return $this;
    }

    public function getCountLike(): ?int
    {
        return $this->count_like;
    }

    public function setCountLike(?int $count_like): self
    {
        $this->count_like = $count_like;

        return $this;
    }
}
