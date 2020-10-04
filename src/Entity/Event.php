<?php

namespace App\Entity;

use App\Repository\EventRepository;
use App\Util\DateTimeProvider;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Pet::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pet;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct(Pet $pet, string $type)
    {
        if ($type !== 'feed' && $type !== 'stroke') {
            throw new \Exception('Invalid event type');
        }

        $this->type = $type;
        $this->createdAt = DateTimeProvider::getCurrentDate();
        $this->pet = $pet;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
}
