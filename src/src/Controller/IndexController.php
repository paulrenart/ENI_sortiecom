<?php

namespace App\Controller;

use App\Entity\Sorties;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * 
     * @Route("/index", name="index")
     */
    public function index()
    {
        $sorties = $this->getDoctrine()
            ->getRepository(Sorties::class)
            ->findAll();
        dump($sorties);
        return $this->render('index/index.html.twig', [
            'sorties' => $sorties,

        ]);
    }
}
