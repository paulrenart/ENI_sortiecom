<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\User;
use App\Form\AdminUserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(User::class);
        $users = $repository->findAll();
        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }
    /**
     * @Route("/admin/user/{id}", name="admin_user_edit", requirements={"id"="\d+"})
     */
    public function admin_user_edit($id, Request $request, UserPasswordEncoderInterface $passwordEncoder) : Response
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(User::class);

        $user = $repository->find($id);
        return $this->render_user_form($user, $request, $passwordEncoder);
    }
    /**
     * @Route("/admin/user/create", name="admin_user_create")
     */
    public function admin_user_create(Request $request, UserPasswordEncoderInterface $passwordEncoder) : Response
    {
        $user = new User;
        return $this->render_user_form($user, $request, $passwordEncoder);
        
    }
    private function render_user_form(User $user, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createForm(
            AdminUserFormType::class,
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
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('admin');
        }
        return $this->render('admin/edit_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/admin/user/delete/{id}", name="delete_user", requirements={"id"="\d+"})
     */
    public function delete_user($id)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(User::class);
        $user = $repository->findOneBy(array('id' => $id));
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('admin');
    }
    /**
     * @Route("/admin/upload", name="upload_user")
     */
    public function upload_user(Request $request, UserPasswordEncoderInterface $passwordEncoder) : Response
    {
        $file = $request->files->get('user_file');

        if (empty($file))
        {
            return new Response("Erreur envoi fichier",
                                Response::HTTP_UNPROCESSABLE_ENTITY, ['content-type' => 'text/plain']);
        }

        $filename = $file->getClientOriginalName();
        $file->move($this->getParameter('imports_directory'), $filename);

        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        $context = [
            CsvEncoder::DELIMITER_KEY => ';',
            CsvEncoder::ENCLOSURE_KEY => '"',
            CsvEncoder::ESCAPE_CHAR_KEY => '\\',
            CsvEncoder::KEY_SEPARATOR_KEY => '|',
        ];

        $content = file_get_contents($this->getParameter('imports_directory').'/'.$filename);
        $data = $serializer->decode($content, 'csv', $context);
        $campus_repository = $this->getDoctrine()->getManager()->getRepository(Campus::class);
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($data as $raw) {
            $raw['campus'] = $campus_repository->findOneBy(array('id' => $raw['campus']));
            $raw['roles'] = explode (',', $raw['roles']);
            $raw['password'] = $passwordEncoder->encodePassword(new User(), $raw['password']);
            $user = $serializer->denormalize($raw, User::class);
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin');
    }
}
