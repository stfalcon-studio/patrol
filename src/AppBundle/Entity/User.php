<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="Violation", mappedBy="author", cascade={"persist", "remove"})
     */
    private $registeredViolations;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->registeredViolations = new ArrayCollection();
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        parent::setEmail($email);
        $this->setUsername($email);
    }

    /**
     * @return Violation[]|ArrayCollection
     */
    public function getRegisteredViolations()
    {
        return $this->registeredViolations;
    }

    /**
     * @param Violation[]|ArrayCollection $violations
     *
     * @return User
     */
    public function setRegisteredViolations($violations)
    {
        $this->registeredViolations = $violations;

        return $this;
    }

    /**
     * @param Violation $violation
     *
     * @return $this
     */
    public function addRegisteredViolation(Violation $violation)
    {
        if (!$this->registeredViolations->contains($violation)) {
            $this->registeredViolations->add($violation);
        }

        return $this;
    }

    /**
     * @param Violation $violation
     *
     * @return $this
     */
    public function removeRegisteredViolation(Violation $violation)
    {
        if ($this->registeredViolations->contains($violation)) {
            $this->registeredViolations->removeElement($violation);
        }

        return $this;
    }
}