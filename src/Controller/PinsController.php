<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PinsController extends AbstractController
{
    #[Route('/', name : 'app_home')]
    public function index(PinRepository $repo): Response
    {
        return $this->render('pins/index.html.twig', ['pins' => $repo->findAll()]);
        // $pin = new Pin;
        // $pin->setTitle('Title 1');
        // $pin->setDescription('Descrfiption 1');

        // $em = $this->getDoctrine()->getManager();
        // $em->persist($pin);
        // $em->flush();

        // $repo = $em->getRepository(Pin::class);
        // $pins = $repo->findAll();
        // return $this->render('pins/index.html.twig', compact('pins'));
    }
    
    #[Route('/create', name : 'app_pins_create', methods: ["GET", "POST"])]
    public function create(Request $req, EntityManagerInterface $em){
        if($req->isMethod('POST')){
            $data = $req->request->all();

            if($this->isCsrfTokenValid('pin_create', $data['_token'])){
                $pin = new Pin;
                $pin->setTitle($data['title']);
                $pin->setDescription($data['description']);
                $em->persist($pin);
                $em->flush();
            }

            return $this->redirect($this->generateUrl('app_home'));
        }

        return $this->render('pins/create.html.twig');
    }
}
