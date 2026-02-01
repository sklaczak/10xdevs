<?php

namespace App\Person\UI\Http;

use App\Person\Application\Command\CreatePerson\CreatePersonCommand;
use App\Person\Application\Command\DeletePerson\DeletePersonCommand;
use App\Person\Application\Command\UpdatePerson\UpdatePersonCommand;
use App\Person\Application\Query\GetPerson\GetPersonQuery;
use App\Person\Application\Query\ListPersons\ListPersonsQuery;
use App\Person\UI\Http\Form\PersonFormModel;
use App\Person\UI\Http\Form\PersonType;
use App\Shared\Application\Bus\CommandBus;
use App\Shared\Application\Bus\QueryBus;
use App\Shared\Domain\Exception\DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/persons')]
final class PersonController extends AbstractController
{
    public function __construct(
        private QueryBus $queryBus,
        private CommandBus $commandBus,
    ) {}

    #[Route('', name: 'person_list', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $q = trim($request->query->getString('q', '')) ?: null;

        $people = $this->queryBus->ask(new ListPersonsQuery($q, 100, 0));

        return $this->render('person/list.html.twig', [
            'persons' => $people,
            'q' => $q,
        ]);
    }

    #[Route('/new', name: 'person_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $model = new PersonFormModel();
        $form = $this->createForm(PersonType::class, $model)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $id = $this->commandBus->dispatch(new CreatePersonCommand(
                    $model->fullName,
                    $model->email,
                    $model->phone
                ));

                $this->addFlash('success', 'Osoba utworzona.');
                return $this->redirectToRoute('person_show', ['id' => $id]);
            } catch (DomainException $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        //var_dump($form->isSubmitted());
        //var_dump($form->isValid());

        return $this->render('person/form.html.twig', [
            'form' => $form->createView(),
            'mode'=> 'create',
        ]);
    }

    #[Route('/{id}', name: 'person_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $person = $this->queryBus->ask(new GetPersonQuery($id));

        return $this->render('person/show.html.twig', [
            'person' => $person,
        ]);
    }

    #[Route('/{id}/edit', name: 'person_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $person = $this->queryBus->ask(new GetPersonQuery($id));

        $model = new PersonFormModel(
            $person->fullName,
            $person->email,
            $person->phone
        );

        $form = $this->createForm(PersonType::class, $model)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commandBus->dispatch(new UpdatePersonCommand(
                $id,
                $model->fullName,
                $model->email,
                $model->phone
            ));

            $this->addFlash('success', 'Osoba zaktualizowana.');
            return $this->redirectToRoute('person_show', ['id' => $id]);
        }

        return $this->render('person/form.html.twig', [
            'form' => $form->createView(),
            'mode'=> 'update',
            'personId' => $id,
        ]);
    }

    #[Route('/{id}', name: 'person_delete', methods: ['POST'])]
    public function delete(int $id): Response
    {
        $this->commandBus->dispatch(new DeletePersonCommand($id));

        $this->addFlash('success', 'Osoba usunięta.');
        return $this->redirectToRoute('person_index');
    }
}
