<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
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
        return $this->render('pins/index.html.twig', ['pins' => $repo->findAll()]);
    }
    
    #[Route('/create', name : 'app_pins_create', methods: ["GET", "POST"])]
    public function create(Request $req, EntityManagerInterface $em){
        // if($req->isMethod('POST')){
        //     $data = $req->request->all();

        //     if($this->isCsrfTokenValid('pin_create', $data['_token'])){
        //         $pin = new Pin;
        //         $pin->setTitle($data['title']);
        //         $pin->setDescription($data['description']);
        //         $em->persist($pin);
        //         $em->flush();
        //     }

        //     return $this->redirectToRoute('app_home');
        // }

        $pin = new Pin;

        $form = $this->createFormBuilder($pin)
            ->add('title', null, [
                'attr' => [
                    'autofocus' => true
                    ] 
                ])
            ->add('description', TextareaType::class)
            // ->add('submit', SubmitType::class, ['label' => 'Create'])
            ->getForm()
        ;

        $form->handleRequest($req);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            // $data = $form->getData();
            
            // $pin = new Pin;
            // $pin->setTitle($data['title']);
            // $pin->setDescription($data['description']);
            $em->persist($pin);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
