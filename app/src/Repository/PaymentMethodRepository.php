<?php

namespace App\Repository;

use App\Entity\PaymentMethod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PaymentMethodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentMethod::class);
    }

    /**
     * Zwraca wszystkie formy płatności z domyślną na początku.
     *
     * @return PaymentMethod[]
     */
    public function findAllOrderedByDefault(): array
    {
        return $this->createQueryBuilder('pm')
            ->orderBy('pm.isDefault', 'DESC')
            ->addOrderBy('pm.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function unsetDefaultForOthers(PaymentMethod $current): void
    {
        $em = $this->getEntityManager();

        $em->createQuery(
            'UPDATE App\Entity\PaymentMethod pm
             SET pm.isDefault = false
             WHERE pm != :current AND pm.isDefault = true'
        )
            ->setParameter('current', $current)
            ->execute();
    }

    public function findDefault(): ?PaymentMethod
    {
        return $this->findOneBy(['isDefault' => true]);
    }
}
