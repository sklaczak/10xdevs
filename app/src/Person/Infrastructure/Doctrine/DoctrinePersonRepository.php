<?php

namespace App\Person\Infrastructure\Doctrine;

use App\Person\Domain\Exception\PersonNotFound;
use App\Person\Domain\Entity\Person;
use App\Person\Domain\Repository\PersonRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrinePersonRepository implements PersonRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function get(int $id): Person
    {
        $person = $this->em->find(Person::class, $id);
        if (!$person) {
            throw PersonNotFound::byId($id);
        }
        return $person;
    }

    public function add(Person $person): void
    {
        $this->em->persist($person);
    }

    public function remove(Person $person): void
    {
        $this->em->remove($person);
    }
}
