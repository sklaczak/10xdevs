<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/demo')]
class DemoCrmController extends AbstractController
{
    #[Route('/invoice', name: 'demo_invoice_show', methods: ['GET'])]
    public function invoiceExample(): Response
    {
        return $this->render('demo/invoice_show.html.twig');
    }

    #[Route('/customer', name: 'demo_customer_show', methods: ['GET'])]
    public function customerExample(): Response
    {
        return $this->render('demo/customer_show.html.twig');
    }

    #[Route('/store', name: 'demo_store_show', methods: ['GET'])]
    public function storeExample(): Response
    {
        return $this->render('demo/store_show.html.twig');
    }
}
