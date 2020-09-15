<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function profile(Request $request, UserPasswordEncoderInterface $passwordEncoder) : Response
    {

        $user = $this->getUser();
        $form = $this->createForm(
            ProfileFormType::class,
            $user,
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            if ($form->get('plainPassword')->getData() != null) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }
            $imageFile = $form->get('photo')->getData();

            if ($imageFile) {
                if ($user->getPhoto())
                {
                    $filesystem = new Filesystem();
                    $filesystem->remove($this->getParameter('images_directory').'/'.$user->getPhoto());
                }
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $user->setPhoto($newFilename);
            }
            
            $user->setActive(True);
            if ($user->getRoles() == null)
            {
                $user->setRoles(['ROLE_USER']);
            }
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('index');
        }

        return $this->render('profile/index.html.twig', [
            'profileForm' => $form->createView(),
            'pseudo' => $user->getPseudo(),
        ]);
    }

    /**
     * @Route("/profile/{id}", name="profile_detail", requirements={"id"="\d+"})
     */
    public function profile_detail($id, Request $request) : Response
    {

        $repository = $this->getDoctrine()->getManager()->getRepository(User::class);
        $user = $repository->findOneBy(array('id' => $id));

        return $this->render('profile/detail.html.twig', [
            'user' => $user,
        ]);
    }
}
