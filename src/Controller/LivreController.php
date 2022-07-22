<?php

namespace App\Controller;

use DateTime;
use App\Entity\Livre;
use App\Form\LivreType;
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
     * @Route("/livre", name="livre_")
     */

class LivreController extends AbstractController
{
    /**
     * @Route("/", name="parution")
     */
    public function parution(LivreRepository $repo)
    {
        $livre=$repo->findAll();
        return $this->render('livre/parutions.html.twig', [
            'livre'=>$livre
        ]);
    }

    /**
     * @Route("/{id<\d+>}", name="show")
     */
    public function show($id, LivreRepository $repo)
    {
        $livre=$repo->find($id);
        return $this->render('livre/showOne.html.twig', [
            'livre'=>$livre
        ]);
    }

        /**
     * @Route("/categorie-{id<\d+>}", name="livre_categorie")
     */
    public function categorieLivres($id, CategorieRepository $repo)
    {
        $categorie=$repo->find($id);
        $categories= $repo->findAll();

        return $this->render('livre/parutions.html.twig', [
            'livre'=>$categorie->getLivres(),
            'categories'=>$categories
        ]);
    }


   


}
