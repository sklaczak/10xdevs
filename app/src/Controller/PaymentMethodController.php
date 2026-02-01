<?php

namespace App\Controller;

use App\Entity\PaymentMethod;
use App\Repository\PaymentMethodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings/payment-methods')]
class PaymentMethodController extends AbstractController
{
    #[Route('/', name: 'payment_method_list', methods: ['GET'])]
    public function list(PaymentMethodRepository $repo): Response
    {
        return $this->render('payment_method/list.html.twig', [
            'paymentMethods' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'payment_method_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        PaymentMethodRepository $paymentMethodRepository,
    ): Response {
        $paymentMethod = new PaymentMethod();

        if ($request->isMethod('POST')) {
            $this->handleForm($paymentMethod, $request);

            $em->persist($paymentMethod);

            if ($paymentMethod->isDefault()) {
                $paymentMethodRepository->unsetDefaultForOthers($paymentMethod);
            }

            $em->flush();

            $this->addFlash('success', 'Forma płatności została dodana.');
            return $this->redirectToRoute('payment_method_list');
        }

        return $this->render('payment_method/form.html.twig', [
            'paymentMethod' => $paymentMethod,
            'mode' => 'create',
        ]);
    }

    #[Route('/{id}/edit', name: 'payment_method_edit', methods: ['GET', 'POST'])]
    public function edit(
        PaymentMethod $paymentMethod,
        Request $request,
        EntityManagerInterface $em,
        PaymentMethodRepository $paymentMethodRepository,
    ): Response {
        if ($request->isMethod('POST')) {
            $this->handleForm($paymentMethod, $request);

            if ($paymentMethod->isDefault()) {
                $paymentMethodRepository->unsetDefaultForOthers($paymentMethod);
            }

            $em->flush();

            $this->addFlash('success', 'Forma płatności została zaktualizowana.');
            return $this->redirectToRoute('payment_method_list');
        }

        return $this->render('payment_method/form.html.twig', [
            'paymentMethod' => $paymentMethod,
            'mode' => 'edit',
        ]);
    }

    #[Route('/{id}/delete', name: 'payment_method_delete', methods: ['POST'])]
    public function delete(PaymentMethod $paymentMethod, EntityManagerInterface $em): Response
    {
        $em->remove($paymentMethod);
        $em->flush();

        $this->addFlash('success', 'Forma płatności została usunięta.');
        return $this->redirectToRoute('payment_method_list');
    }

    private function handleForm(PaymentMethod $paymentMethod, Request $request): void
    {
        $name = $request->request->get('name');
        $isDefault = (bool) $request->request->get('isDefault', false);

        $paymentMethod->setName($name);
        $paymentMethod->setIsDefault($isDefault);
    }
}
