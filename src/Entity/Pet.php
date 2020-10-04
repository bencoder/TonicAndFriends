<?php

namespace App\Entity;

use App\Repository\PetRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=PetRepository::class)
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="type", type="string")
 * @DiscriminatorMap({
 *  "cat" = "\App\Entity\Pet\Cat",
 *  "dog" = "\App\Entity\Pet\Dog",
 *  "snake" = "\App\Entity\Pet\Snake"
 * })
 * @Serializer\ExclusionPolicy("all")
 */
abstract class Pet
{
    abstract protected function getHappinessPerStroke(): float;
    abstract protected function getHungerPerFeed(): float;
    
    /**
     * @Serializer\VirtualProperty()
     */
    abstract public function getHappinessReduceRate(): float;
    
    /**
     * @Serializer\VirtualProperty()
     */
    abstract public function getHungerIncreaseRate(): float;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $userId;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="pet", cascade="persist")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     */
    private $events;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Expose
     */
    private $createdAt;

    public function __construct(int $userId, string $name)
    {
        $this->userId = $userId;
        $this->name = $name;
        $this->events = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function feed(): void
    {
        $this->events->add(new Event($this, 'feed'));
    }

    public function stroke(): void
    {
        $this->events->add(new Event($this, 'stroke'));
    }

    private function getHappinessReduction(DateTimeInterface $prevTime, DateTimeInterface $nextTime) : float
    {
        $timeDifference = $nextTime->getTimestamp() - $prevTime->getTimestamp();
        $timeDifference /= 3600;    //The happiness reduction rates are per-hour

        return $timeDifference * $this->getHappinessReduceRate();
    }

    private function getHungerIncrease(DateTimeInterface $prevTime, DateTimeInterface $nextTime) : float
    {
        $timeDifference = $nextTime->getTimestamp() - $prevTime->getTimestamp();
        $timeDifference /= 3600;    //The happiness reduction rates are per-hour

        return $timeDifference * $this->getHungerIncreaseRate();
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function getHappiness(): float
    {
        $happiness = 0.5;  
        $lastEventTime = $this->createdAt;
        
        $strokes = $this->events->filter(
            fn($event) => $event->getType() === 'stroke'
        );

        foreach($strokes as $strokeEvent) {
            $happiness -= $this->getHappinessReduction($lastEventTime, $strokeEvent->getCreatedAt());
            $happiness += $this->getHappinessPerStroke();
            $happiness = max(0, min(1,$happiness));
            $lastEventTime = $strokeEvent->getCreatedAt();
        }

        $happiness -= $this->getHappinessReduction($lastEventTime, new \DateTime());
        $happiness = max(0, min(1,$happiness));

        return $happiness;
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function getHunger(): float
    {
        $hunger = 0.5;
        $lastEventTime = $this->createdAt;

        $feeds = $this->events->filter(
            fn($event) => $event->getType() === 'feed'
        );

        foreach($feeds as $feedEvent) {
            $hunger += $this->getHungerIncrease($lastEventTime, $feedEvent->getCreatedAt());
            $hunger -= $this->getHungerPerFeed();
            $hunger = max(0, min(1,$hunger));
            $lastEventTime = $feedEvent->getCreatedAt();
        };

        $hunger += $this->getHungerIncrease($lastEventTime, new \DateTime());
        $hunger = max(0, min(1,$hunger));

        return $hunger;
    }
}
