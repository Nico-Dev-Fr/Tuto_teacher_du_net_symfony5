<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PinsController extends AbstractController
{
    #[Route('/', name : 'app_home', methods: ["GET"])]
    public function index(PinRepository $repo): Response
    {
        return $this->render('pins/index.html.twig', ['pins' => $repo->findBy([],['createdAt' => 'DESC'])]);
    }

    #[Route('/pins/{id<[0-9]+>}', name : 'app_pins_show', methods: ["GET"])]
    public function show(Pin $pin, $id): Response
    {   
        return $this->render('pins/show.html.twig', compact('pin'));
    }
    
    #[Route('/create', name : 'app_pins_create', methods: ["GET", "POST"])]
    public function create(Request $req, EntityManagerInterface $em, UserRepository $userRepo) : Response
    {

        $pin = new Pin;

        $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($req);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $pin->setUser($this->getUser());
            $em->persist($pin);
            $em->flush();
            $this->addFlash('success','Successfull');

            return $this->redirectToRoute('app_pins_show', ['id' => $pin->getId()]);
        }

        return $this->render('pins/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/pins/{id<[0-9]+>}/edit', name : 'app_pins_edit', methods: ['GET', 'POST', 'PUT'])]
    public function edit(Request $req, Pin $pin, EntityManagerInterface $em) : Response
    {

        $form = $this->createForm(PinType::class, $pin, [
            'method' => 'POST'
        ]);

        $form->handleRequest($req);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $em->flush();
            $this->addFlash('success','Successfull update');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/edit.html.twig', [
            'pin' => $pin,
            'form' => $form->createView()
        ]);
    }

    #[Route('/pins/{id<[0-9]+>}/delete', name : 'app_pins_delete', methods: ['GET'])]
    public function delete(Pin $pin, EntityManagerInterface $em): Response
    {
        $em->remove($pin);
        $em->flush();
        $this->addFlash('info','Successfull delete');

        return $this->redirectToRoute('app_home');
    }
}
