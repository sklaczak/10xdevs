<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Enum\InvoiceType;
use App\Repository\InvoiceRepository;
use App\Repository\PaymentMethodRepository;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mobile/invoices/cost', name: 'mobile_cost_invoice_')]
class MobileCostInvoiceController extends AbstractController
{
    #[Route('/', name: 'list', methods: ['GET'])]
    public function list(InvoiceRepository $repo): Response
    {
        return $this->render('mobile/list.html.twig', [
            'invoices' => $repo->findBy(['type' => InvoiceType::COST]),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        PersonRepository $personRepo,
        PaymentMethodRepository $paymentMethodRepo
    ): Response {
        $invoice = new Invoice();
        $invoice->setType(InvoiceType::COST);

        if ($request->isMethod('POST')) {
            $this->handleMobileForm($invoice, $request, $personRepo, $paymentMethodRepo);
            $em->persist($invoice);
            $em->flush();

            $this->addFlash('success', 'Faktura kosztowa została dodana (mobile).');
            return $this->redirectToRoute('mobile_cost_invoice_list');
        }

        return $this->render('mobile/form.html.twig', [
            'invoice'         => $invoice,
            'mode'            => 'create',
            'people'          => $personRepo->findAll(),
            'paymentMethods'  => $paymentMethodRepo->findAllOrderedByDefault(),
        ]);
    }

    private function handleMobileForm(
        Invoice $invoice,
        Request $request,
        PersonRepository $personRepo,
        PaymentMethodRepository $paymentMethodRepo
    ): void {
        $amount          = $request->request->get('amount');
        $description     = $request->request->get('description');
        $personId        = $request->request->get('personId');
        $paymentMethodId = $request->request->get('paymentMethodId');

        /** @var UploadedFile|null $photo */
        $photo = $request->files->get('photo');

        $invoice->setNumber('M' . ((new \DateTimeImmutable())->format('Y-m-d H:i:s')));

        // Kwota + opis
        $invoice->setGrossAmount($amount);
        if (method_exists($invoice, 'setDescription')) {
            $invoice->setDescription($description);
        }

        // Osoba wprowadzająca
        if ($personId) {
            $person = $personRepo->find($personId);
            if (method_exists($invoice, 'setPerson')) {
                $invoice->setPerson($person);
            }
        }

        // Forma płatności
        if ($paymentMethodId) {
            $paymentMethod = $paymentMethodRepo->find($paymentMethodId);
            if (method_exists($invoice, 'setPaymentMethod')) {
                $invoice->setPaymentMethod($paymentMethod);
            }
        }

        // Data automatycznie teraz
        if (method_exists($invoice, 'setIssuedAt')) {
            $invoice->setIssuedAt(new \DateTimeImmutable());
        }

        // Zdjęcie – na razie tylko przyjmujemy, NIE zapisujemy
        if ($photo instanceof UploadedFile) {
            // TODO: w przyszłości zapisać zdjęcie do storage / pola w encji
            // np. $invoice->setPhotoPath($filename);
        }
    }
}
