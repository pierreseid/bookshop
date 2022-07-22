<?php

namespace App\Controller;

use DateTime;
use App\Entity\Livre;
use App\Form\LivreType;
use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\LivreRepository;
use App\Repository\CategorieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

    /**
     * @Route("/admin", name="admin_")
     */

class AdminController extends AbstractController
{
     /**
     * @Route("/add", name="ajout_livre")
     */
        public function ajout(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid()){
            $livre->setdateDeCreation ( new DateTime("now"));

            $file = $form->get('photoCouv')->getData();
            $fileName = $slugger->slug($livre->getTitre()) . uniqid() . '.' . $file->guessExtension();

            try{
                $file->move($this->getParameter('photo_livre'), $fileName);
            }catch(FileException $e){
            }
            $livre->setPhotoCouv($fileName);

            $manager=$doctrine->getManager();
            $manager->persist($livre);
            $manager->flush();

             $this->addFlash('success', "La fiche du livre a bien été ajoutée");

            return $this->redirectToRoute('admin_gestion_livre');
        }
        return $this->render('admin/formLivre.html.twig', [
            'formLivre'=>$form->createView()
        ]);
    }

    /**
     * @Route("/gestion-livre", name="gestion_livre")
     */
    public function adminLivres(LivreRepository $repo): Response
    {
         if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
             $this->addFlash('error', "Veuillez vous connecter pour accéder à la page");
             return $this->redirectToRoute('app_login');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
             $this->addFlash('error', "Vous n'avez pas les droits pour accéder à cette page");
             return $this->redirectToRoute('app_home');
        }

        $livres=$repo->findAll();

        return $this->render('admin/gestionLivre.html.twig', [
            'livres'=>$livres
        ]);
    }

    /**
     * @Route("/{id<\d+>}", name="detail_livre")
     */
    public function show($id, LivreRepository $repo)
    {
        $livre=$repo->find($id);
        return $this->render('admin/detail-livre.html.twig', [
            'livre'=>$livre
        ]);
    }

    /**
     * @Route("/update_livre/{id<\d+>}", name="update_livre")
     */
    public function update($id, LivreRepository $repo, Request $request, SluggerInterface $slugger) 
    {
         if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
             $this->addFlash('error', "Veuillez vous connecter pour accéder à la page");
             return $this->redirectToRoute('app_login');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
             $this->addFlash('error', "Vous n'avez pas les droits pour accéder à cette page");
             return $this->redirectToRoute('app_home');
        }

        $livre = $repo->find($id);
        $form =$this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if($form->get('photoCouv')->getData()){
                $file = $form->get('photoCouv')->getData();
                $fileName = $slugger->slug($livre->getTitre()) . uniqid() . '.' . $file->guessExtension();

            try{
                $file->move($this->getParameter('photo_livre'), $fileName);
            }catch(FileException $e){
            }
            $livre->setPhotoCouv($fileName);
            }

            $repo->add($livre,1);
            
        $this->addFlash('success', "La fiche a bien été mise à jour");

        return $this->redirectToRoute("admin_gestion_livre");
        }

        return $this->render('admin/formLivre.html.twig', [
            'formLivre'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/delete_livre_{id<\d+>}", name="delete_livre")
     */
    public function delete($id, LivreRepository $repo) : Response
    {
         if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
             $this->addFlash('error', "Veuillez vous connecter pour accéder à la page");
             return $this->redirectToRoute('app_login');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
             $this->addFlash('error', "Vous n'avez pas les droits pour accéder à cette page");
             return $this->redirectToRoute('app_home');
        }

                $livre = $repo->find($id);
                $repo->remove($livre, 1); 

                $this->addFlash('success', "La fiche a bien été supprimée");

                return $this->redirectToRoute("admin_gestion_livre");
    }

    /**
     * @Route("/categorie-ajout", name="ajout_categorie")
     */
    public function ajoutCategorie(Request  $request, CategorieRepository $repo) : Response
    {
         if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
             $this->addFlash('error', "Veuillez vous connecter pour accéder à la page");
             return $this->redirectToRoute('app_login');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
             $this->addFlash('error', "Vous n'avez pas les droits pour accéder à cette page");
             return $this->redirectToRoute('app_home');
        }
        
            $categorie = new Categorie();
            $form = $this->createForm(CategorieType:: class, $categorie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $repo->add($categorie, 1);
                
                $this->addFlash('success', "La catégorie a bien été ajoutée");
                return $this->redirectToRoute('admin_ajout_categorie');
            }
                return $this->render('admin/formCategorie.html.twig', [
                'formCategorie'=>$form->createView(),
        ]);
    } 













}
