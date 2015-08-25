<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="violations")
 */
class Violation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, name="photoFilename")
     *
     * @var string $photoFilename
     */
    private $photoFilename;

    /**
     *
     * @Assert\Image(
     *      maxWidth = 5000,
     *      maxHeight = 5000,
     *      mimeTypes = {"image/jpeg", "image/png"},
     *      maxSize = "5M",
     *      minHeight = 100,
     *      minWidth = 100
     * )
     *
     * @var File $photo
     */
    private $photo;

    /**
     * @var string $carNumber
     *
     * @ORM\Column(name="car_number", type="string", nullable=true)
     */
    private $carNumber;

    /**
     * @var float $latitude Latitude
     *
     * @ORM\Column(type="decimal", precision=18, scale=15)
     */
    private $latitude;

    /**
     * @var float $longitude Longitude
     *
     * @ORM\Column(type="decimal", precision=18, scale=15)
     */
    private $longitude;

    /**
     * @var bool $approved
     *
     * @ORM\Column(type="boolean")
     */
    private $approved;

    /**
     * @var \DateTime $created
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="registeredViolations")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     **/
    private $author;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->getId()) {
            $title = 'Violation '.$this->getId();
        } else {
            $title = 'New violation';
        }

        return $title;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCarNumber()
    {
        return $this->carNumber;
    }

    /**
     * @param string $carNumber
     */
    public function setCarNumber($carNumber)
    {
        $this->carNumber = $carNumber;
    }

    /**
     * @return boolean
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * @param boolean $approved
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param User $author
     *
     * @return $this
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return string
     */
    public function getPhotoFilename()
    {
        return $this->photoFilename;
    }

    /**
     * @param string $photoFilename
     */
    public function setPhotoFilename($photoFilename)
    {
        $this->photoFilename = $photoFilename;
    }

    /**
     * @return File
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param File $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    /**
     * @return null|string
     */
    public function getAbsolutePath()
    {
        if (is_null($this->photoFilename)) {
            return null;
        }

        return $this->getUploadRootDir().'/'.$this->photoFilename;
    }

    /**
     * @return null|string
     */
    public function getWebPath()
    {
        if (is_null($this->photoFilename)) {
            return null;
        }

        return $this->getUploadDir().$this->photoFilename;
    }

    /**
     * @return string
     */
    public function getSubPath()
    {
        return date('Y/m/d/');
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->photo) {
            $this->photoFilename = $this->getSubPath().uniqid().'.'.$this->photo->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->photo) {
            return;
        }

        $path = explode('/', $this->photoFilename);
        $file = array_pop($path);
        $uploadDir = $this->getUploadRootDir().implode('/', $path);
        $this->photo->move($uploadDir, $file);

        unset($this->photo);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            @unlink($file);
        }
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    protected function getUploadDir()
    {
        return 'uploads/violation_images/';
    }

    /**
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }
}