<?php

namespace App\Repository;

use App\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Log>
 *
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    public function countOfLogsLine($serviceNames, $statusCode, $startDate, $endDate): int
    {

        $qb = $this->createQueryBuilder('l')
            ->select('COUNT(l.id)');

        // Apply filters
        if ($serviceNames && is_array($serviceNames) && count($serviceNames)) {
            $qb->andWhere('l.serviceName IN (:serviceNames)')
                ->setParameter('serviceNames', $serviceNames);
        }
        if (!empty($statusCode)) {
            $qb->andWhere('l.statusCode = (:statusCodes)')
                ->setParameter('statusCodes', $statusCode);
        }
        if (!empty($startDate)) {
            $qb->andWhere('l.createdAt >= :startDate')
                ->setParameter('startDate', new \DateTimeImmutable($startDate));
        }
        if (!empty($endDate)) {
            $qb->andWhere('l.createdAt < :endDate')
                ->setParameter('endDate', (new \DateTimeImmutable($endDate))->modify('+ 1 day'));
        }

        // Execute the query and return the count
        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
