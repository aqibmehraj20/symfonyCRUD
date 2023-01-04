<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonFormType;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

final class PersonController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private PersonRepository       $repository,
    )
    {
        $log = new Logger('PersonController');
        $log->pushHandler(new StreamHandler('var/log/your.log', Level::Info));
        $log->info('your log is missing');
    }

    #[Route('/overview', name: 'overview')]
    public function overview(): Response
    {
        $person = $this->repository->findAll();
        return $this->render('overview.html.twig', [
            'data' => $person
        ]);

    }

    #[Route('/create_person', name: 'create_person')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(PersonFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $person = new Person();
            $person->setName($data->getName());
            $person->setAddress($data->getAddress());
            $this->em->persist($person);
            $this->em->flush();

            return $this->redirect('/update_person/'.$person->getId());
        }
        return $this->render('edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/update_person/{id}', name: 'update_person')]
    public function update(Request $request, int $id): Response
    {
        $person = $this->repository->find(["id" => $id]);

        $form = $this->createForm(PersonFormType::class, $person);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $person->setName($data->getName());
            $person->setAddress($data->getAddress());
            $this->em->persist($person);
            $this->em->flush();

            return $this->redirect('/');
        }
        return $this->render('edit.html.twig', [
            'form' => $form,
            'id' => $form->getData()->getId(),
        ]);
    }

    #[Route('/delete_person/{id}', name: 'delete_person')]
    public function delete(Request $request, int $id): Response
    {
        $person = $this->repository->find(["id" => $id]);
        $this->em->remove($person);
        $this->em->flush();
        return $this->redirect('/');
    }
}
