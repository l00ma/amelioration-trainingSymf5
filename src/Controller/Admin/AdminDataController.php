<?php

namespace App\Controller\Admin;

use App\Repository\AdminRepository;
//use App\Form\AdminType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminDataController extends AbstractController
{

    /**
     * @var AdminRepository
     */
    private $repository;

    public function __construct(AdminRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/admin/data", name="admin.data.edit", methods="GET|POST")
     */
    public function edit(Request $request, UserPasswordHasherInterface $passwordEncoder)
    {
        $admin = $this->getUser();
        if ($request->isMethod('POST')) {
            if (!empty($request->request->get('user'))) {
                if (!empty($request->request->get('pass1')) or !empty($request->request->get('pass2'))) {
                    if ($request->request->get('pass1') == $request->request->get('pass2')) {
                        $admin->setUsername($request->request->get('user'));
                        $admin->setPassword($passwordEncoder->hashPassword($admin, $request->request->get('pass1')));
                        $this->em->flush();
                        $this->addFlash('success', 'Les modifications ont été effectuées');
                    } else {
                        $this->addFlash('error', 'Les 2 mots de passe sont différents');
                    }
                } else {
                    $this->addFlash('error', 'Le mot de passe ne doit pas être vide');
                }
            } else {
                $this->addFlash('error', 'Vous devez saisir un nom d\'utilisateur');
            }
        }

        return $this->render('admin/data/edit.html.twig');
    }
}
