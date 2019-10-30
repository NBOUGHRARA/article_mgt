<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=10, max=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=10)
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url()
     */
    private $image;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="article", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ArticleLikes", mappedBy="article", orphanRemoval=true)
     */
    private $articleLikes;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->user = new ArrayCollection();
        $this->articleLikes = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

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
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ArticleLikes[]
     */
    public function getArticleLikes(): Collection
    {
        return $this->articleLikes;
    }

    public function addArticleLike(ArticleLikes $articleLike): self
    {
        if (!$this->articleLikes->contains($articleLike)) {
            $this->articleLikes[] = $articleLike;
            $articleLike->setArticle($this);
        }

        return $this;
    }

    public function removeArticleLike(ArticleLikes $articleLike): self
    {
        if ($this->articleLikes->contains($articleLike)) {
            $this->articleLikes->removeElement($articleLike);
            // set the owning side to null (unless already changed)
            if ($articleLike->getArticle() === $this) {
                $articleLike->setArticle(null);
            }
        }

        return $this;
    }

    public function isLikedByUser(User $user) : bool
    {
        foreach ($this->articleLikes as $like) {
            if ($like->getUser() === $user) {
                return true;
            }
        }
        return false;
    }
}
