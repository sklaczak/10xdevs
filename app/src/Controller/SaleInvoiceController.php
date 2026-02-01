<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Enum\InvoiceType;
use App\Repository\CustomerRepository;
use App\Repository\InvoiceRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/invoices/sale')]
class SaleInvoiceController extends AbstractController
{
    #[Route('/', name: 'sale_invoice_list', methods: ['GET'])]
    public function list(InvoiceRepository $repo): Response
    {
        return $this->render('invoice/sale/list.html.twig', [
            'invoices' => $repo->findBy(['type' => 'sale']),
        ]);
    }

    #[Route('/new', name: 'sale_invoice_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        CustomerRepository $customerRepository,
        ProjectRepository $projectRepository
    ): Response {
        $invoice = new Invoice();

        if ($request->isMethod('POST')) {
            $this->handleForm($invoice, $request, $customerRepository, $projectRepository);
            $em->persist($invoice);
            $em->flush();

            $this->addFlash('success', 'Faktura została dodana.');
            return $this->redirectToRoute('sale_invoice_list');
        }

        return $this->render('invoice/sale/form.html.twig', [
            'invoice' => $invoice,
            'mode' => 'create',
            'customers' => $customerRepository->findAll(),
            'projects'  => $projectRepository->findAll(),
        ]);
    }

    #[Route('/{id}/edit', name: 'sale_invoice_edit', methods: ['GET', 'POST'])]
    public function edit(
        Invoice $invoice,
        Request $request,
        EntityManagerInterface $em,
        CustomerRepository $customerRepository,
        ProjectRepository $projectRepository,
    ): Response {
        if ($request->isMethod('POST')) {
            $this->handleForm($invoice, $request, $customerRepository, $projectRepository);
            $em->flush();

            $this->addFlash('success', 'Faktura została zaktualizowana.');
            return $this->redirectToRoute('invoice_list');
        }

        return $this->render('invoice/cost/form.html.twig', [
            'invoice' => $invoice,
            'mode' => 'edit',
            'customers' => $customerRepository->findAll(),
            'projects'  => $projectRepository->findAll(),
        ]);
    }

    #[Route('/{id}/delete', name: 'sale_invoice_delete', methods: ['POST'])]
    public function delete(Invoice $invoice, EntityManagerInterface $em): Response
    {
        $em->remove($invoice);
        $em->flush();

        $this->addFlash('success', 'Faktura została usunięta.');
        return $this->redirectToRoute('sale_invoice_list');
    }

private function handleForm(
    Invoice $invoice,
    Request $request,
    CustomerRepository $customerRepository,
    ProjectRepository $projectRepository,
): void {
    $number = $request->request->get('number');
    $customerName = $request->request->get('customerName');
    $amount = $request->request->get('amount');
    $status = $request->request->get('status');
    $issuedAt = $request->request->get('issuedAt');
    $dueDate = $request->request->get('dueDate');
    $netto = $request->request->get('netAmount');
    $brutto = $request->request->get('grossAmount');
    $vat = $request->request->get('taxAmount');
    $taxRate = $request->request->get('taxRate');

    $invoice->setNumber($number);
    $invoice->setCustomerName($customerName);
    $invoice->setStatus($status);
    $invoice->setNetAmount($netto);
    $invoice->setGrossAmount($brutto);
    $invoice->setTaxAmount($vat);
    $invoice->setTaxRate($taxRate);

    $customerId = $request->request->get('customerId');
    $projectId  = $request->request->get('projectId');

    $customer = null;
    if ($customerId) {
        $customer = $customerRepository->find($customerId);
    }
    $invoice->setCustomer($customer);

    $project = null;
    if ($projectId) {
        $project = $projectRepository->find($projectId);
    }
    $invoice->setProject($project);

    $type = 'sale';

    if ($type && in_array($type, ['sale', 'cost'], true)) {
        $invoice->setType(InvoiceType::from($type));
    }

    $issuedAtDate = \DateTimeImmutable::createFromFormat('Y-m-d', $issuedAt) ?: new \DateTimeImmutable();
    $invoice->setIssuedAt($issuedAtDate);

    if ($dueDate) {
        $dueDateDate = \DateTimeImmutable::createFromFormat('Y-m-d', $dueDate);
        $invoice->setDueDate($dueDateDate ?: null);
    } else {
        $invoice->setDueDate(null);
    }
    }
}
