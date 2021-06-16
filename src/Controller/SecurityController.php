<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\RegistrationFormType;

class SecurityController extends AbstractController
{
    #[Route('/login', name : 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    
    #[Route('/logout', name : 'app_logout')]
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    
    #[Route('/account', name : 'app_account_edit')]
    public function edit(Request $req, EntityManagerInterface $em) : Response
    {
        $form = $this->createForm(RegistrationFormType::class, $this->getUser());

        $form->handleRequest($req);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $em->flush();
            $this->addFlash('success','Successfull update');

            return $this->redirectToRoute('app_account_edit');
        }

        return $this->render('account/edit.html.twig', [
            'user' => $this->getUser(),
            'form' => $form->createView()
        ]);
    }
        
    #[Route('/account_delete', name : 'app_account_delete')]
    public function delete(Request $req, EntityManagerInterface $em) : Response
    {
        $em->remove($this->getUser());
        $em->flush();
        $this->addFlash('info','Successfull delete');

        return $this->redirectToRoute('app_login');
    }
}
