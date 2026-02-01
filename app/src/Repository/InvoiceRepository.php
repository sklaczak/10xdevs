<?php

namespace App\Repository;

use App\Entity\Invoice;
use App\Enum\InvoiceType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    public function countCreatedBetween(\DateTimeImmutable $from, \DateTimeImmutable $to): int
    {
        return (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->andWhere('i.issuedAt >= :from')
            ->andWhere('i.issuedAt < :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countCreatedBetweenByType(
        \DateTimeImmutable $from,
        \DateTimeImmutable $to,
        InvoiceType $type
    ): int {
        return (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->andWhere('i.issuedAt >= :from')
            ->andWhere('i.issuedAt < :to')
            ->andWhere('i.type = :type')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('type', $type)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Zwraca dane do wykresu:
     * [
     *   ['day' => '2025-11-26', 'count' => 4],
     *   ...
     * ]
     */
    public function getDailyCountsForLastDays(int $days): array
    {
        $today = new \DateTimeImmutable('today');
        $from  = $today->modify(sprintf('-%d days', $days - 1));
        $to    = $today->modify('+1 day');

        $invoices = $this->createQueryBuilder('i')
            ->andWhere('i.issuedAt >= :from')
            ->andWhere('i.issuedAt < :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getResult();

        $map = [];

        foreach ($invoices as $invoice) {
            /** @var \App\Entity\Invoice $invoice */
            $day = $invoice->getIssuedAt()->format('Y-m-d');
            if (!isset($map[$day])) {
                $map[$day] = 0;
            }
            $map[$day]++;
        }

        $result = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $from->modify("+$i days")->format('Y-m-d');
            $result[] = [
                'day'   => $date,
                'count' => $map[$date] ?? 0,
            ];
        }

        return $result;
    }

    /**
     * Ostatnie faktury z dołączoną osobą (która kupiła materiały).
     */
    public function findRecentWithPerson(int $limit = 5): array
    {
        return $this->createQueryBuilder('i')
            ->addSelect('p')
            ->leftJoin('i.person', 'p')
            ->orderBy('i.issuedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
