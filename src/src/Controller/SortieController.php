<?php

namespace App\Controller;

use App\Entity\Inscriptions;
use App\Entity\Sorties;
use App\Form\SortieFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie/create", name="create")
     */
    public function create(Request $request)
    {
        $sortie = new Sorties();
        $form = $this->createForm(
            SortieFormType::class,
            $sortie,
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setOrganisateur($this->getUser());
            $sortie->setEtat(1);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('index');
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/sortie/edit/{id}", name="edit", requirements={"id"="\d+"})
     */
    public function edit($id, Request $request)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Sorties::class);
        $sortie = $repository->findOneBy(array('id' => $id));
        $form = $this->createForm(
            SortieFormType::class,
            $sortie,
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('index');
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/sortie/subscribe/{id}", name="subscribe", requirements={"id"="\d+"})
     */
    public function subscribe($id, Request $request)
    {
        $inscription = new Inscriptions();
        $inscription->setUser($this->getUser());
        $repository = $this->getDoctrine()->getManager()->getRepository(Sorties::class);
        $sortie = $repository->findOneBy(array('id' => $id));
        $inscription->setSorties($sortie);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($inscription);
        $entityManager->flush();
        return $this->redirectToRoute('index');
    }
    /**
     * @Route("/sortie/unsubscribe/{id}", name="unsubscribe", requirements={"id"="\d+"})
     */
    public function unsubscribe($id, Request $request)
    {
        $sortie_repository = $this->getDoctrine()->getManager()->getRepository(Sorties::class);
        $sortie = $sortie_repository->findOneBy(array(
            'id' => $id,
        ));
        $inscription_repository = $this->getDoctrine()->getManager()->getRepository(Inscriptions::class);
        $inscription = $inscription_repository->findOneBy(array(
            'sorties' => $sortie,
            'user' => $this->getUser()
        ));
        $em = $this->getDoctrine()->getManager();
        $em->remove($inscription);
        $em->flush();
        return $this->redirectToRoute('index');
    }
    /**
     * @Route("/sortie/delete/{id}", name="delete", requirements={"id"="\d+"})
     */
    public function delete($id)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Sorties::class);
        $sortie = $repository->findOneBy(array('id' => $id));
        $em = $this->getDoctrine()->getManager();
        $em->remove($sortie);
        $em->flush();
        return $this->redirectToRoute('index');
    }
}
