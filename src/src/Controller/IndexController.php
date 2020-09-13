<?php

namespace App\Controller;

use App\Entity\Sorties;
use App\Form\SortiesFilterFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * 
     * @Route("/index", name="index")
     */
    public function index(Request $request)
    {
        $search_filters = new Sorties();
        $form = $this->createForm(
            SortiesFilterFormType::class,
            $search_filters,
        );

        $sorties_repository = $this->getDoctrine()->getRepository(Sorties::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sorties = $sorties_repository->findFilteredSorties($form, $this->getUser());
        } else {
            $sorties = $sorties_repository->findAll();
        }
        
        return $this->render('index/index.html.twig', [
            'sorties' => $sorties,
            'user_sorties' => $sorties_repository->findUserSorties($this->getUser()),
            'search_form' => $form->createView(),
        ]);
    }
}
