<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AlbumRepository")
 */
class Album
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AlbumCategory", inversedBy="albums")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="albums")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userid;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $views;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $collect_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $comment_count;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descript;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $images;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $thumb;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AlbumComment", mappedBy="album")
     */
    private $albumComments;

    public function __construct()
    {
        $this->views = 0;
        $this->collect_count = 0;
        $this->comment_count = 0;
        $this->created_at = new \Datetime();
        $this->updated_at = new \Datetime();
        $this->albumComments = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCategory(): ?AlbumCategory
    {
        return $this->category;
    }

    public function setCategory(?AlbumCategory $category): self
    {
        $this->category = $category;

        return $this;
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

    public function getUserid(): ?User
    {
        return $this->userid;
    }

    public function setUserid(?User $userid): self
    {
        $this->userid = $userid;

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

    public function getCollectCount(): ?int
    {
        return $this->collect_count;
    }

    public function setCollectCount(?int $collect_count): self
    {
        $this->collect_count = $collect_count;

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

    public function getDescript(): ?string
    {
        return $this->descript;
    }

    public function setDescript(?string $descript): self
    {
        $this->descript = $descript;

        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getThumb(): ?string
    {
        return $this->thumb;
    }

    public function setThumb(?string $thumb): self
    {
        $this->thumb = $thumb;

        return $this;
    }

    /**
     * @return Collection|AlbumComment[]
     */
    public function getAlbumComments(): Collection
    {
        return $this->albumComments;
    }

    public function addAlbumComment(AlbumComment $albumComment): self
    {
        if (!$this->albumComments->contains($albumComment)) {
            $this->albumComments[] = $albumComment;
            $albumComment->setAlbum($this);
        }

        return $this;
    }

    public function removeAlbumComment(AlbumComment $albumComment): self
    {
        if ($this->albumComments->contains($albumComment)) {
            $this->albumComments->removeElement($albumComment);
            // set the owning side to null (unless already changed)
            if ($albumComment->getAlbum() === $this) {
                $albumComment->setAlbum(null);
            }
        }

        return $this;
    }
}
