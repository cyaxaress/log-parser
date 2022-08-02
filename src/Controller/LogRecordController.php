<?php

namespace App\Controller;

use App\Repository\LogRecordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LogRecordController extends AbstractController
{
    #[Route('/count', name: 'log_record_count')]
    public function count(LogRecordRepository $logRecordRepository, Request $request): JsonResponse
    {
        $query = $logRecordRepository->createQueryBuilder('lg');

        if ($request->query->has('serviceNames')) {
            $query->andWhere('lg.service in (:serviceNames)')
                ->setParameter('serviceNames', $request->query->get("serviceNames"));
        }

        if ($request->query->has('statusCode')) {
            $query->andWhere('lg.status = :statusCode')
                ->setParameter('statusCode', $request->query->get("statusCode"));
        }

        if ($request->query->has('startDate')){
            $date = new \DateTime($request->query->get('startDate'));
            $query->andWhere('lg.date >= :startDate')
                ->setParameter('startDate', $date) ;
        }

        if ($request->query->has('endDate')) {
            $date = new \DateTime($request->query->get('endDate'));
            $query->andWhere('lg.date <= :endDate')
                ->setParameter('endDate', $date);
        }
        $query->select("count(lg.id)");

        return $this->json([
            'counter' => $query->getQuery()->getSingleScalarResult(),
        ]);
    }
}
