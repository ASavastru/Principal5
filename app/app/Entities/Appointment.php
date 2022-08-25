<?php

declare(strict_types=1);

namespace App\Entities;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping AS ORM;

#[ORM\Entity]
#[ORM\Table('appointments')]
class Appointment extends BaseEntity
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    protected int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'appointments', fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    protected User $user;

    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: 'appointments', fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(name: 'location_id', referencedColumnName: 'id', nullable: false)]
    protected Location $location;

    #[ORM\Column(name: 'date', type: Types::DATE_MUTABLE, nullable: false)]
    protected \DateTime $date;
}