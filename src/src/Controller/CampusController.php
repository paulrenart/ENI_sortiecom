<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CampusController extends AbstractController
{
    /**
     * @Route("/campus", name="campus")
     */
    public function index(Request $request)
    {
        $campus = $this->getDoctrine()
            ->getRepository(Campus::class)
            ->findAll();
        $camp = new Campus();
        $form = $this->createForm(
            CampusFormType::class,
            $camp,
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($camp);
            $entityManager->flush();
            return $this->redirectToRoute('campus');
        }

        return $this->render('campus/index.html.twig', [
            'campus' => $campus,
            'campusForm' => $form->createView(),
        ]);
    }
    /**
     * @Route("/campus/delete/{id}", name="delete", requirements={"id"="\d+"})
     */
    public function delete($id)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Campus::class);
        $camp = $repository->findOneBy(array('id' => $id));
        $em = $this->getDoctrine()->getManager();
        $em->remove($camp);
        $em->flush();
        return $this->redirectToRoute('campus');
    }
    /**
     * @Route("/campus/detail/{id}", name="detail", requirements={"id"="\d+"})
     */
    public function detail($id, Request $request)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Campus::class);
        $camp = $repository->findOneBy(array('id' => $id));
        $form = $this->createForm(
            CampusFormType::class,
            $camp,
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($camp);
            $entityManager->flush();
            return $this->redirectToRoute('campus');
        }

        return $this->render('campus/detail.html.twig', [
            'campusForm' => $form->createView(),
        ]);
    }
}
