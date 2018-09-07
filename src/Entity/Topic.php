<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\TopicRepository")
 * @ORM\HasLifecycleCallbacks() 
 */
class Topic
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="主题名称不能为空")
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TopicCategory", inversedBy="topics")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $views;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $comment_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $collect_count;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TopicComment", mappedBy="topic")
     */
    private $topicComments;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="topics")
     */
    private $userid;

    public function __construct()
    {
        $this->topicComments = new ArrayCollection();
    }
    /**
     * 生命周期
     */
    /**
     * @ORM\PrePersist()
     */
    public function setInitValue()
    {
        $this->views = 0;
        $this->comment_count = 0;
        $this->collect_count = 0;
        $this->created_at = new \Datetime();
        $this->status = 1;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCategory(): ?TopicCategory
    {
        return $this->category;
    }

    public function setCategory(?TopicCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(?int $views): self
    {
        $this->views = $views;

        return $this;
    }

    public function getCommentCount(): ?int
    {
        return $this->comment_count;
    }

    public function setCommentCount(?int $comment_count): self
    {
        $this->comment_count = $comment_count;

        return $this;
    }

    public function getCollectCount(): ?int
    {
        return $this->collect_count;
    }

    public function setCollectCount(?int $collect_count): self
    {
        $this->collect_count = $collect_count;

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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|TopicComment[]
     */
    public function getTopicComments(): Collection
    {
        return $this->topicComments;
    }

    public function addTopicComment(TopicComment $topicComment): self
    {
        if (!$this->topicComments->contains($topicComment)) {
            $this->topicComments[] = $topicComment;
            $topicComment->setTopic($this);
        }

        return $this;
    }

    public function removeTopicComment(TopicComment $topicComment): self
    {
        if ($this->topicComments->contains($topicComment)) {
            $this->topicComments->removeElement($topicComment);
            // set the owning side to null (unless already changed)
            if ($topicComment->getTopic() === $this) {
                $topicComment->setTopic(null);
            }
        }

        return $this;
    }

    public function getUserid(): ?User
    {
        return $this->userid;
    }

    public function setUserid(?User $userid): self
    {
        $this->userid = $userid;

        return $this;
    }
}
