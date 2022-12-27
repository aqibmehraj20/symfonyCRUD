<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Table;

#[Table(name: 'Persons')]
#[ORM\Entity(repositoryClass: PersonRepository::class)]
final class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Column(name: 'id', type: Types::INTEGER, nullable: False)]
    private int $id;

    #[Column(name: 'person_name', type: Types::STRING, nullable: True)]
    private ?string $name = null;

    #[Column(name: 'person_address', type: Types::STRING, nullable: True)]
    private ?string $address = null;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string|null $address
     */
    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }
}
