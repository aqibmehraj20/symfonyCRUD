<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $repository
    ){
    }

    #[Route('/create_user', name: 'create_user')]
    public function index(UserPasswordHasherInterface $passwordHasher, Request $request)
    {
        $form = $this->createForm(UserFormType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $user  = new User();
            $user->setEmail($data->getEmail());
            $plainTextPassword = $data->getPassword();
            $hashPassword = $passwordHasher->hashPassword($user,$plainTextPassword);
            $user->setPassword($hashPassword);
            $user->setRoles($data->getRoles());
            $this->em->persist($user);
            $this->em->flush();
            return $this->redirect('/');
        }
        return $this->render('register.html.twig', [
            'form' => $form
        ]);

    }
}