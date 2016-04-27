<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;


/**
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 */
class Post
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string")
     * @Assert\Email()
     */
    private $authorEmail;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     * @Assert\File(
     *              maxSize = "4M",
     *              mimeTypes = {"image/*"},
     *              maxSizeMessage = "The file is too large ({{ size }}).Allowed maximum size is {{ limit }}",
     *              mimeTypesMessage = "The mime type of the file is invalid ({{ type }}).
     *              Allowed mime types are {{ types }}")
     */
    private $imageName;

    /**
     * @var string
     * @Assert\NotBlank(message="post.blank_content")
     * @Assert\Length(min = "10", minMessage = "post.too_short_content")
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var string
     * @Assert\NotBlank(message="post.blank_content")
     * @Assert\Length(min = "10", minMessage = "post.too_short_content")
     * @ORM\Column(name="shortText", type="text")
     */
    private $shortText;

    /**
     * @var \DateTime $created
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime $updated
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @var \DateTime $contentChanged
     * @ORM\Column(name="contentChanged", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"title", "content"})
     */
    private $contentChanged;

    /**
     * @var array
     *
     * @ORM\Column(name="marks", type="array", nullable=true)
     */
    private $marks;

    /**
     * @var float
     * @ORM\Column(name="rating", type="float", nullable=true)
     */
    private $rating;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="post", orphanRemoval=true)
     * @ORM\OrderBy({"created" = "DESC"})
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tag", inversedBy="posts")
     */
    private $tags;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Is the given User the author of this Post?
     *
     * @param User $user
     *
     * @return bool
     */
    public function isAuthor(User $user)
    {
        return $user->getEmail() == $this->getAuthorEmail();
    }

    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    public function setAuthorEmail($authorEmail)
    {
        $this->authorEmail = $authorEmail;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Post
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Post
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Post
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Post
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get contentChanged
     *
     * @return \DateTime
     */
    public function getContentChanged()
    {
        return $this->contentChanged;
    }

    /**
     * Set contentChanged
     *
     * @param \DateTime $contentChanged
     *
     * @return Post
     */
    public function setContentChanged($contentChanged)
    {
        $this->contentChanged = $contentChanged;

        return $this;
    }

    /**
     * Get marks
     *
     * @return array
     */
    public function getMarks()
    {
        return $this->marks;
    }

    /**
     * Set marks
     *
     * @param array $marks
     *
     * @return Post
     */
    public function setMarks($marks)
    {
        $this->marks = $marks;

        return $this;
    }

    public function addMark($mark)
    {
        $this->marks[] = $mark;
        $count = count($this->marks);
        $total = 0;
        for ($i = 0; $i < $count; $i++) {
            $total = $total + $this->marks[$i];
        }
        $this->rating = $total / $count;
    }

    /**
     * Get rating
     *
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set rating
     *
     * @param float $rating
     *
     * @return Post
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Add comment
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return Post
     */
    public function addComment(\AppBundle\Entity\Comment $comment)
    {
        $this->getComments()->add($comment);

        return $this;
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Remove comment
     *
     * @param \AppBundle\Entity\Comment $comment
     */
    public function removeComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Add tag
     *
     * @param \AppBundle\Entity\Tag $tag
     *
     * @return Post
     */
    public function addTag(\AppBundle\Entity\Tag $tag)
    {
        $this->getTags()->add($tag);

        return $this;
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Remove tag
     *
     * @param \AppBundle\Entity\Tag $tag
     */
    public function removeTag(\AppBundle\Entity\Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get shortText
     *
     * @return string
     */
    public function getShortText()
    {
        return $this->shortText;
    }

    /**
     * Set shortText
     *
     * @param string $shortText
     *
     * @return Post
     */
    public function setShortText($shortText)
    {
        $this->shortText = $shortText;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param $imageFile
     * @return $this
     */
    public function setImageFile($imageFile)
    {
        $this->imageFile = $imageFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * @param string $imageName
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }
}
