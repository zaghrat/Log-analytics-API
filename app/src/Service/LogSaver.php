<?php

namespace App\Service;

use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;

class LogSaver
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(array $logData): void
    {
        // Example: Save log data into the database using Doctrine ORM
        $logEntity = new Log();
        $logEntity->setServiceName($logData['service_name']);
        $logEntity->setStatusCode($logData['status_code']);
        $logEntity->setCreatedAt($logData['requestedAt']);

        $this->entityManager->persist($logEntity);
    }
}