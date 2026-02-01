<?php

namespace App\Controller;

use App\Enum\InvoiceType;
use App\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(InvoiceRepository $invoiceRepository): Response
    {
        $today = new \DateTimeImmutable('today');
        $tomorrow = $today->modify('+1 day');

        // KPI – ile faktur dzisiaj
        $todayAll  = $invoiceRepository->countCreatedBetween($today, $tomorrow);
        $todayCost = $invoiceRepository->countCreatedBetweenByType($today, $tomorrow, InvoiceType::COST);
        $todaySale = $invoiceRepository->countCreatedBetweenByType($today, $tomorrow, InvoiceType::SALE);

        // Wykres: ostatnie 7 dni
        $lastDaysStats = $invoiceRepository->getDailyCountsForLastDays(7);

        // Ostatnie faktury z osobą (kto zakupił materiały)
        $recentInvoices = $invoiceRepository->findRecentWithPerson(5);

        return $this->render('dashboard/index.html.twig', [
            'todayAll'       => $todayAll,
            'todayCost'      => $todayCost,
            'todaySale'      => $todaySale,
            'lastDaysStats'  => $lastDaysStats,
            'recentInvoices' => $recentInvoices,
        ]);
    }
}
