<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    /**
     * @var ProduitRepository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(ProduitRepository $repository, ObjectManager $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/produit", name="produit.index")
     * @return Response
     */
    public function index(): Response
    {
        $produit = $this->repository->findAll();
        return $this->render('produit/index.html.twig', [
            'current_menu' => 'produit',
            'produits' => $produit
        ]);
    }

    /**
     * @Route("/produit/new", name="produit.new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($produit);
            $this->em->flush();
            $this->addFlash('success', 'Produit ajouté avec succès');
            return $this->redirectToRoute('produit.index');
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/produit/edit/{id}", name="produit.edit", methods="GET|POST")
     * @param Produit $produit
     * @param Request $request
     * @return Response
     */
    public function edit(Produit $produit, Request $request): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->flush();
            $this->addFlash('succes', 'Produit modifié avec succès');
            return $this->redirectToRoute('produit.index');
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/produit/delete/{id}", name="produit.delete", methods="DELETE")
     * @param Produit $produit
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function delete(Produit $produit, Request $request){
        if($this->isCsrfTokenValid('delete' . $produit->getId(), $request->get('_token'))){
            $this->em->remove($produit);
            $this->em->flush();
            $this->addFlash('succes', 'Produit supprimé avec succès');
        }
        return $this->redirectToRoute('produit.index');
    }

    /**
     * @Route("/produit/show/{id}", name="produit.show")
     */
    public function show(Produit $produit){
        return $this->render('produit/show.html.twig');
    }

}
