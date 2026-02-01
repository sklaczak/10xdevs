<?php

namespace App\Controller;

use App\Entity\UninvoicedPurchase;
use App\Repository\UninvoicedPurchaseRepository;
use App\Repository\PaymentMethodRepository;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/purchases/uninvoiced')]
class UninvoicedPurchaseController extends AbstractController
{
    #[Route('/', name: 'uninvoiced_purchase_list', methods: ['GET'])]
    public function list(UninvoicedPurchaseRepository $repo): Response
    {
        return $this->render('uninvoiced_purchase/list.html.twig', [
            'purchases' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'uninvoiced_purchase_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        PaymentMethodRepository $paymentMethodRepo,
        PersonRepository $personRepo
    ): Response {
        $purchase = new UninvoicedPurchase();

        if ($request->isMethod('POST')) {
            $this->handleForm($purchase, $request, $paymentMethodRepo, $personRepo);
            $em->persist($purchase);
            $em->flush();

            $this->addFlash('success', 'Zakup nierozliczony został dodany.');
            return $this->redirectToRoute('uninvoiced_purchase_list');
        }

        return $this->render('uninvoiced_purchase/form.html.twig', [
            'purchase'        => $purchase,
            'mode'            => 'create',
            'paymentMethods'  => $paymentMethodRepo->findAll(),
            'people'          => $personRepo->findAll(),
        ]);
    }

    #[Route('/{id}/edit', name: 'uninvoiced_purchase_edit', methods: ['GET', 'POST'])]
    public function edit(
        UninvoicedPurchase $purchase,
        Request $request,
        EntityManagerInterface $em,
        PaymentMethodRepository $paymentMethodRepo,
        PersonRepository $personRepo
    ): Response {
        if ($request->isMethod('POST')) {
            $this->handleForm($purchase, $request, $paymentMethodRepo, $personRepo);
            $em->flush();

            $this->addFlash('success', 'Zakup nierozliczony został zaktualizowany.');
            return $this->redirectToRoute('uninvoiced_purchase_list');
        }

        return $this->render('uninvoiced_purchase/form.html.twig', [
            'purchase'        => $purchase,
            'mode'            => 'edit',
            'paymentMethods'  => $paymentMethodRepo->findAll(),
            'people'          => $personRepo->findAll(),
        ]);
    }

    #[Route('/{id}/delete', name: 'uninvoiced_purchase_delete', methods: ['POST'])]
    public function delete(UninvoicedPurchase $purchase, EntityManagerInterface $em): Response
    {
        $em->remove($purchase);
        $em->flush();

        $this->addFlash('success', 'Zakup nierozliczony został usunięty.');
        return $this->redirectToRoute('uninvoiced_purchase_list');
    }

    private function handleForm(
        UninvoicedPurchase $purchase,
        Request $request,
        PaymentMethodRepository $paymentMethodRepo,
        PersonRepository $personRepo
    ): void {
        $amount          = $request->request->get('amount');
        $paymentMethodId = $request->request->get('paymentMethodId');
        $personId        = $request->request->get('personId');
        $description     = $request->request->get('description');
        $expenseDate     = $request->request->get('expenseDate');

        $purchase->setAmount($amount);
        $purchase->setDescription($description);

        // PaymentMethod
        if ($paymentMethodId) {
            $paymentMethod = $paymentMethodRepo->find($paymentMethodId);
            $purchase->setPaymentMethod($paymentMethod);
        } else {
            $purchase->setPaymentMethod(null);
        }

        // Person
        if ($personId) {
            $person = $personRepo->find($personId);
            $purchase->setPerson($person);
        } else {
            $purchase->setPerson(null);
        }

        // Data zakupu
        if ($expenseDate) {
            $expenseDateObj = \DateTimeImmutable::createFromFormat('Y-m-d', $expenseDate) ?: new \DateTimeImmutable();
            $purchase->setExpenseDate($expenseDateObj);
        } else {
            // jak nie podano – dziś
            $purchase->setExpenseDate(new \DateTimeImmutable());
        }

        // updatedAt
        $purchase->setUpdatedAt(new \DateTimeImmutable());
    }
}
