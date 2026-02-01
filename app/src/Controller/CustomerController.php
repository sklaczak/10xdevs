<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/settings/customers')]
class CustomerController extends AbstractController
{
    #[Route('', name: 'customer_list', methods: ['GET'])]
    public function list(CustomerRepository $customerRepository): Response
    {
        return $this->render('customer/list.html.twig', [
            'customers' => $customerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'customer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $customer = new Customer();

        if ($request->isMethod('POST')) {
            $this->handleForm($customer, $request);

            $em->persist($customer);
            $em->flush();

            $this->addFlash('success', 'Kontrahent został dodany.');
            return $redirect = $this->redirectToRoute('customer_list');
        }

        return $this->render('customer/form.html.twig', [
            'mode'     => 'create',
            'customer' => $customer,
        ]);
    }

    #[Route('/{id}/edit', name: 'customer_edit', methods: ['GET', 'POST'])]
    public function edit(
        Customer $customer,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if ($request->isMethod('POST')) {
            $this->handleForm($customer, $request);

            $em->flush();

            $this->addFlash('success', 'Kontrahent został zaktualizowany.');
            return $this->redirectToRoute('customer_list');
        }

        return $this->render('customer/form.html.twig', [
            'mode'     => 'edit',
            'customer' => $customer,
        ]);
    }

    #[Route('/{id}/delete', name: 'customer_delete', methods: ['POST'])]
    public function delete(
        Customer $customer,
        EntityManagerInterface $em
    ): Response {
        // Uwaga: jeśli są projekty / faktury powiązane z kontrahentem,
        // tu możesz potrzebować dodatkowej walidacji albo CASCADE.
        $em->remove($customer);
        $em->flush();

        $this->addFlash('success', 'Kontrahent został usunięty.');
        return $this->redirectToRoute('customer_list');
    }

    private function handleForm(Customer $customer, Request $request): void
    {
        $name = trim((string) $request->request->get('name', ''));

        $customer->setName($name);
    }
}
