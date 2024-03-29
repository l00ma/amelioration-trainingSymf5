<?php

namespace App\Controller\Admin;

use App\Entity\Images;
use App\Repository\PropertyRepository;
use App\Entity\Property;
use App\Form\PropertyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class AdminPropertyController extends AbstractController
{
    /**
     * @var PropertyRepository
     */
    private $repository;

    public function __construct(PropertyRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/admin", name="admin.property.index")
     * @return \Symfony\Componenet\HttpFoundation\Response
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {

        $properties = $paginator->paginate(
            $this->repository->findAll(),
            $request->query->getInt('page', 1),
            25
        );
        return $this->render('admin/property/index.html.twig', compact('properties'));
    }

    /**
     * @Route("/admin/property/create", name="admin.property.new")
     */
    public function new(Request $request)
    {
        $property = new Property;
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('images')->getData();
            foreach ($images as $image) {
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $img = new Images();
                $img->setName($fichier);
                $property->addImage($img);
            }

            $this->em->persist($property);
            $this->em->flush();
            $this->addFlash('success', 'Bien crée avec succès');
            return $this->redirectToRoute('property.index');
        }
        return $this->render('admin/property/new.html.twig', [
            'property' => $property,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/property/{id}", name="admin.property.edit", methods="GET|POST")
     * @param Property $property
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Property $property, Request $request)
    {
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('images')->getData();
            foreach ($images as $image) {
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $img = new Images();
                $img->setName($fichier);
                $property->addImage($img);
            }

            $this->em->flush();
            $this->addFlash('success', 'Bien modifié avec succès');
            return $this->redirectToRoute('property.index');
        }


        return $this->render('admin/property/edit.html.twig', [
            'property' => $property,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/property/delete/{id}", name="admin.property.delete", methods="POST")
     * @param Property $property
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Property $property, Request $request)
    {

        if ($this->isCsrfTokenValid('delete' . $property->getId(), $request->get('_token'))) {
            #on recupere les images associées au bien property
            $noms = $property->getImages();
            if ($noms) {
                foreach ($noms as $nom) {
                    $nom_image = $nom->getName();
                    #on recupere le nom des images et on les efface du disque
                    if (file_exists($this->getParameter('images_directory') . '/' . $nom_image)) {
                        unlink($this->getParameter('images_directory') . '/' . $nom_image);
                    }
                    if (file_exists($this->getParameter('cache_thumb') . '/' . $nom_image)) {
                        unlink($this->getParameter('cache_thumb') . '/' . $nom_image);
                    }
                    if (file_exists($this->getParameter('cache_fixe') . '/' . $nom_image)) {
                        unlink($this->getParameter('cache_fixe') . '/' . $nom_image);
                    }
                }
            }
            #on supprime le bien de la bdd (les images seront supprimées en même temps grace à la cascade)
            $this->em->remove($property);
            $this->em->flush();
            $this->addFlash('success', 'Bien supprimé avec succès');
        }
        return $this->redirectToRoute('property.index');
    }

    /**
     * @Route("/admin/image/delete/{id}", name="admin.image.delete", methods="DELETE")
     */
    public function deleteImage(Images $image, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
            $nom_image = $image->getName();
            if (file_exists($this->getParameter('images_directory') . '/' . $nom_image)) {
                unlink($this->getParameter('images_directory') . '/' . $nom_image);
            }
            if (file_exists($this->getParameter('cache_thumb') . '/' . $nom_image)) {
                unlink($this->getParameter('cache_thumb') . '/' . $nom_image);
            }
            if (file_exists($this->getParameter('cache_fixe') . '/' . $nom_image)) {
                unlink($this->getParameter('cache_fixe') . '/' . $nom_image);
            }
            $this->em->remove($image);
            $this->em->flush();
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token invalide'], 400);
        }
    }
}
