<?php

namespace AppBundle\Entity;

use AppBundle\DBAL\Types\VideoStatusType;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="violations", indexes={@ORM\Index(name="video_idx", columns={"videoFilename"})})
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
     * @ORM\Column(type="string", length=255, name="photoFilename", nullable=true)
     *
     * @var string $photoFilename
     */
    private $photoFilename;

    /**
     *
     * @Assert\Image(
     *      maxWidth  = 5000,
     *      maxHeight = 5000,
     *      mimeTypes = {"image/jpeg", "image/png"},
     *      maxSize   = "5M",
     *      minHeight = 100,
     *      minWidth  = 100
     * )
     *
     * @var File $photo
     */
    private $photo;

    /**
     * @ORM\Column(type="string", length=255, name="VideoFilename", nullable=true)
     *
     * @var string $photoFilename
     */
    private $videoFilename;

    /**
     * @Assert\File(
     *     mimeTypes = {"video/mp4", "video/quicktime", "video/3gpp"},
     *     mimeTypesMessage = "Please upload a valid video"
     * )
     *
     * @var File $photo
     */
    private $video;

    /**
     * @var \DateTime|null $date Date
     *
     * @ORM\Column(type="datetime", name="date", nullable=true)
     *
     * @Assert\DateTime()
     */
    private $date;

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
     * @var bool $approved Approved
     *
     * @ORM\Column(type="boolean")
     */
    private $approved;

    /**
     * @var \DateTime $created Created at
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(name="status", type="VideoStatusType", nullable=false)
     * @DoctrineAssert\Enum(entity="AppBundle\DBAL\Types\VideoStatusType")
     */
    private $status = VideoStatusType::READY;

    /**
     * @var User $author Author
     *
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
     * @return null|string
     */
    public function getVideoAbsolutePath()
    {
        if (is_null($this->videoFilename)) {
            return null;
        }

        return $this->getVideoUploadRootDir().'/'.$this->videoFilename;
    }

    /**
     * @return null|string
     */
    public function getVideoWebPath()
    {
        if (is_null($this->videoFilename)) {
            return null;
        }

        return $this->getVideoUploadDir().$this->videoFilename;
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
        if (null !== $this->video) {
            $this->videoFilename = $this->getSubPath().uniqid().'.'.$this->video->guessExtension();
            if ($this->video->getMimeType() == 'video/3gpp') {
                $this->setStatus(VideoStatusType::WAITING);
            }
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null !== $this->photo) {
            $path = explode('/', $this->photoFilename);
            $file = array_pop($path);
            $uploadDir = $this->getUploadRootDir().implode('/', $path);
            $this->photo->move($uploadDir, $file);

            unset($this->photo);
        }

        if (null === $this->video) {
            return;
        }

        $path = explode('/', $this->videoFilename);
        $file = array_pop($path);
        $uploadDir = $this->getVideoUploadRootDir().implode('/', $path);
        $this->video->move($uploadDir, $file);

        unset($this->video);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (($file = $this->getAbsolutePath()) || ($file = $this->getVideoAbsolutePath())) {
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
    public function getVideoFilename()
    {
        return $this->videoFilename;
    }

    /**
     * @param string $videoFilename
     */
    public function setVideoFilename($videoFilename)
    {
        $this->videoFilename = $videoFilename;
    }

    /**
     * @return File
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @param File $video
     */
    public function setVideo($video)
    {
        $this->video = $video;
    }

    /**
     * @return \DateTime|null
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime|null $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getUploadDir()
    {
        return 'uploads/violation_images/';
    }

    /**
     * @return string
     */
    public function getUploadRootDir()
    {
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    /**
     * @return string
     */
    public function getVideoUploadDir()
    {
        return 'uploads/violation_videos/';
    }

    /**
     * @return string
     */
    public function getVideoUploadRootDir()
    {
        return __DIR__.'/../../../web/'.$this->getVideoUploadDir();
    }
}
