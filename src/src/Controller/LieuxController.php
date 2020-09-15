<?php

namespace App\Controller;

use App\Entity\Lieux;
use App\Form\LieuxFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LieuxController extends AbstractController
{
    /**
     * @Route("/lieux/create", name="lieux")
     */
    public function index(Request $request)
    {
        $lieu = new Lieux();
        $form = $this->createForm(
            LieuxFormType::class,
            $lieu,
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($lieu);
            $entityManager->flush();
            return $this->redirectToRoute('lieux');
        }

        return $this->render('lieux/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
