<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\CustomerRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings/projects')]
class ProjectController extends AbstractController
{
    #[Route('', name: 'project_list', methods: ['GET'])]
    public function list(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/list.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'project_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        CustomerRepository $customerRepository
    ): Response {
        $project = new Project();

        if ($request->isMethod('POST')) {
            $this->handleForm($project, $request, $customerRepository);

            $em->persist($project);
            $em->flush();

            $this->addFlash('success', 'Projekt został dodany.');
            return $this->redirectToRoute('project_list');
        }

        return $this->render('project/form.html.twig', [
            'mode'      => 'create',
            'project'   => $project,
            'customers' => $customerRepository->findAll(),
        ]);
    }

    #[Route('/{id}/edit', name: 'project_edit', methods: ['GET', 'POST'])]
    public function edit(
        Project $project,
        Request $request,
        EntityManagerInterface $em,
        CustomerRepository $customerRepository
    ): Response {
        if ($request->isMethod('POST')) {
            $this->handleForm($project, $request, $customerRepository);

            $em->flush();

            $this->addFlash('success', 'Projekt został zaktualizowany.');
            return $this->redirectToRoute('project_list');
        }

        return $this->render('project/form.html.twig', [
            'mode'      => 'edit',
            'project'   => $project,
            'customers' => $customerRepository->findAll(),
        ]);
    }

    #[Route('/{id}/delete', name: 'project_delete', methods: ['POST'])]
    public function delete(
        Project $project,
        EntityManagerInterface $em
    ): Response {
        // Analogicznie: jeśli faktury są przypięte do projektu,
        // tu trzeba rozważyć walidację / blokowanie usunięcia.
        $em->remove($project);
        $em->flush();

        $this->addFlash('success', 'Projekt został usunięty.');
        return $this->redirectToRoute('project_list');
    }

    private function handleForm(
        Project $project,
        Request $request,
        CustomerRepository $customerRepository
    ): void {
        $name       = trim((string) $request->request->get('name', ''));
        $customerId = $request->request->get('customerId');

        $project->setName($name);

        $customer = null;
        if ($customerId) {
            $customer = $customerRepository->find($customerId);
        }

        $project->setCustomer($customer);
    }
}
