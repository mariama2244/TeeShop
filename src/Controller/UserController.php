<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Form\RegisterFormType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
  #[Route('/inscription', name: 'register', methods: ['GET', 'POST'])]
  public function register(Request $request, UserRepository $repository, UserPasswordHasherInterface $passwordHasher): Response 
  {
     $user = new User();
     $form = $this->createForm(RegisterFormType::class, $user)
         ->handleRequest($request);

         // Condition to see your ur bdd
    if($form->isSubmitted() && $form->isValid()) {

        # set les propriétés qui ne sont pas dans le formulaire et oubligatoires en BDD
        $user->setCreatedAt(new DateTime());
        $user->setUpdatedAt(new DateTime());

        # Set les roles du user. Cette propriétés est un array[].
        $user->setRoles(['ROLE_USER']);

        # on dois resseter manuellement la valeur du password, car par défault il n'est pas hashé
        #pour cele, nous devons utiliser une méthode de hashage appelée hashPassword() :
        # => cette méthode attend 2 arguements : user, $plainpassword
        $user->setPassword(
            $passwordHasher->hashPassword($user, $user->getPassword())
        );

        $repository->save($user, true);

        $this->addFlash('success', "Votre inscription a été bien enregistrée !!!");

        return $this->redirectToRoute('show_home');


    }

     return $this->render('user/register_form.html.twig', [
         'form' => $form->createView()
     ]); 
  } // end register()
} // end claee{}
