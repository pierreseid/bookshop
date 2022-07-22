<?php

namespace App\Controller;

use App\Repository\LivreRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(LivreRepository $repo)
    {
        $livre = $repo->findBy([], ['dateDeCreation'=>'DESC'],4);
        return $this->render('home/home.html.twig', [
            'livre'=>$livre]);
    }
}
