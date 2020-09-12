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
            $dumped = $form->get('subscribed')->getData();
            $sorties = $this->filteredSorties($form);
        } else {
            $dumped = [];
            $sorties = $sorties_repository->findAll();
        }
        
        return $this->render('index/index.html.twig', [
            'sorties' => $sorties,
            'user_sorties' => $this->getUserSorties(),
            'search_form' => $form->createView(),
            'dumped' => $dumped
        ]);
    }

    private function getUserSorties()
    {
        $user_sorties = [];
        foreach($this->getUser()->getInscriptions() as $inscription)
        {
            $user_sorties[] = $inscription->getSorties();
        }
        return $user_sorties;
    }

    private function filteredSorties($form)
    {
        $sorties_repository = $this->getDoctrine()->getRepository(Sorties::class);
        $sorties = $sorties_repository->findAll();
        if ($form->get('campus')->getData() != null)
        {
            foreach ($sorties as $key=>$sortie) {
                if ($sortie->getCampus() != $form->get('campus')->getData())
                {
                    unset($sorties[$key]);
                }
            }
        }
        if ($form->get('nom')->getData() != null)
        {
            foreach ($sorties as $key=>$sortie) {
                if (strpos($sortie->getNom(),$form->get('nom')->getData()) == false)
                {
                    unset($sorties[$key]);
                }
            }
        }
        if ($form->get('date_debut')->getData() != null)
        {
            foreach ($sorties as $key=>$sortie) {
                if ($sortie->getDateDebut() < $form->get('date_debut')->getData())
                {
                    unset($sorties[$key]);
                }
            }
        }
        if ($form->get('date_fin')->getData() != null)
        {
            foreach ($sorties as $key=>$sortie) {
                if ($sortie->getDateFin() > $form->get('date_fin')->getData())
                {
                    unset($sorties[$key]);
                }
            }
        }
        if (!$form->get('subscribed')->getData())
        {
            $user_sorties = $this->getUserSorties();
            foreach ($sorties as $key=>$sortie) {
                if (in_array($sortie,$user_sorties))
                {
                    unset($sorties[$key]);
                }
            }
        }
        if (!$form->get('notSubscribed')->getData())
        {
            $user_sorties = $this->getUserSorties();
            foreach ($sorties as $key=>$sortie) {
                if (!in_array($sortie,$user_sorties))
                {
                    unset($sorties[$key]);
                }
            }
        }
        if (!$form->get('owned')->getData())
        {
            foreach ($sorties as $key=>$sortie) {
                if ($sortie->getOrganisateur() == $this->getUser())
                {
                    unset($sorties[$key]);
                }
            }
        }
        if ($form->get('expired')->getData())
        {
            foreach ($sorties as $key=>$sortie) {
                if ($sortie->getDateDebut() > new \DateTime('now'))
                {
                    unset($sorties[$key]);
                }
            }
        }
        return $sorties;
    }
}
