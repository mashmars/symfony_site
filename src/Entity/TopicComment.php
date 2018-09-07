<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\TopicCommentRepository")
 */
class TopicComment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Topic", inversedBy="topicComments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $topic;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="topicComments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userid;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="回复内容不能为空")
     */
    private $content;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $like_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $unlink_count;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TopicCommentLike", mappedBy="comment")
     */
    private $topicCommentLikes;

    public function __construct()
    {
        $this->topicCommentLikes = new ArrayCollection();
        $this->like_count = 0;
        $this->unlink_count = 0;
        $this->status = 1;
        $this->created_at = new \Datetime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTopic(): ?Topic
    {
        return $this->topic;
    }

    public function setTopic(?Topic $topic): self
    {
        $this->topic = $topic;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getLikeCount(): ?int
    {
        return $this->like_count;
    }

    public function setLikeCount(?int $like_count): self
    {
        $this->like_count = $like_count;

        return $this;
    }

    public function getUnlinkCount(): ?int
    {
        return $this->unlink_count;
    }

    public function setUnlinkCount(?int $unlink_count): self
    {
        $this->unlink_count = $unlink_count;

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
     * @return Collection|TopicCommentLike[]
     */
    public function getTopicCommentLikes(): Collection
    {
        return $this->topicCommentLikes;
    }

    public function addTopicCommentLike(TopicCommentLike $topicCommentLike): self
    {
        if (!$this->topicCommentLikes->contains($topicCommentLike)) {
            $this->topicCommentLikes[] = $topicCommentLike;
            $topicCommentLike->setComment($this);
        }

        return $this;
    }

    public function removeTopicCommentLike(TopicCommentLike $topicCommentLike): self
    {
        if ($this->topicCommentLikes->contains($topicCommentLike)) {
            $this->topicCommentLikes->removeElement($topicCommentLike);
            // set the owning side to null (unless already changed)
            if ($topicCommentLike->getComment() === $this) {
                $topicCommentLike->setComment(null);
            }
        }

        return $this;
    }
}
