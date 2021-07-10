<?php

namespace App\Entity;

use App\Repository\LockDateRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LockDateRepository::class)
 */
class LockDate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", unique=true)
     */
    private $locked_date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLockedDate(): ?DateTimeInterface
    {
        return $this->locked_date;
    }

    public function setLockedDate(DateTimeInterface $locked_date): self
    {
        $this->locked_date = $locked_date;

        return $this;
    }
}
