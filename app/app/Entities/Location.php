<?php

declare(strict_types=1);

namespace App\Entities;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping AS ORM;

#[ORM\Entity]
#[ORM\Table('locations')]

class Location extends BaseEntity
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    protected int $id;

    #[ORM\Column(name: 'city', type: Types::STRING, length: 50, nullable: false)]
    protected string $city;

    #[ORM\OneToMany(mappedBy: 'location', targetEntity: Appointment::class, fetch: 'EXTRA_LAZY')]
    protected $appointments;
}