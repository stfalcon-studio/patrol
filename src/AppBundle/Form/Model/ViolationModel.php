<?php

namespace AppBundle\Form\Model;

use AppBundle\DBAL\Types\VideoRecordingType;
use AppBundle\DBAL\Types\VideoStatusType;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class ViolationModel
 */
class ViolationModel
{
    /**
     * @var File $photo
     */
    private $video;

    /**
     * @var \DateTime|null $date Date
     */
    private $date;

    /**
     * @var string $carNumber
     */
    private $carNumber;

    /**
     * @var float $latitude Latitude
     */
    private $latitude;

    /**
     * @var float $longitude Longitude
     */
    private $longitude;

    /**
     * @var bool $approved Approved
     */
    private $approved;

    /**
     * @var string $status Status
     */
    private $status = VideoStatusType::READY;

    /**
     * @var string $recordingType Recording type
     */
    private $recordingType = VideoRecordingType::UPLOAD;

    /**
     * @var string $authorEmail
     */
    private $authorEmail;

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
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getRecordingType()
    {
        return $this->recordingType;
    }

    /**
     * @param string $recordingType
     */
    public function setRecordingType($recordingType)
    {
        $this->recordingType = $recordingType;
    }

    /**
     * @return string
     */
    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    /**
     * @param string $authorEmail
     */
    public function setAuthorEmail($authorEmail)
    {
        $this->authorEmail = $authorEmail;
    }
}
