<?php

namespace App\Controller;

use DateTime;
use App\Entity\Commande;
use App\Entity\CommandeDetail;
use App\Repository\LivreRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommandeDetailRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CommandeController extends AbstractController
{
    /**
     * @Route("/commande", name="app_commande")
     */
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }

    /**
     * @Route("/passer-ma-commande", name="passer_commande")
     */
    public function passerCommande(
        SessionInterface $session, 
        LivreRepository $repoLivre, 
        CommandeRepository $repoCom, 
        CommandeDetailRepository $repoDet,
        EntityManagerInterface $manager ): Response
    {
        $commande = new Commande();
        $panier =$session->get('panier', []);

        $user = $this->getUser();

        if (!$user) {
            $this->addFlash("error", "Veuillez vous connecter ou vous inscrire pour passer la commande!");
            return $this->redirectToRoute("app_home");
        }
        if (empty($panier)) {
            $this->addFlash("error", "Votre panier est vide, vous ne pouvez pas passer commande!");
            return $this->redirectToRoute("livre_parution");
        }

        $dataPanier =[];
        $total = 0;

        foreach ($panier as $id => $quantite) {
            $livre =$repoLivre->find($id);
            $dataPanier[]=
            [
                "livre"=>$livre,
                "quantite"=> $quantite,
                "sousTotal"=> $livre->getPrix()*$quantite
            ];
            $total += $livre->getPrix()*$quantite;
        }

        $commande->setUser($user)
                    ->setDateDeCommande(new DateTime('now'))
                    ->setMontant($total)
                ;

        $repoCom->add($commande);

        foreach ($dataPanier as $key => $value) {
            $commandeDetail =new CommandeDetail();
            $livre = $value["livre"];
            $quantite = $value["quantite"];
            $sousTotal = $value["sousTotal"];

            $commandeDetail->setCommande($commande)
                            ->setLivre($livre)
                            ->setQuantite($quantite)
                            ->setPrix($sousTotal)
                            ;
            
            $repoDet->add($commandeDetail);
        }

        $manager->flush();
        $session->remove("panier");
        $this->addFlash("success", "Votre commande a bien été enregistrée!");
        return $this->redirectToRoute("livre_parution");

    }
}
