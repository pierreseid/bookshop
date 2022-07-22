<?php

namespace App\Controller;

use App\Repository\LivreRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    /**
     * @Route("/panier", name="panier_")
     */

class PanierController extends AbstractController
{
    /**
     * @Route("/", name="show")
     */
    public function show(SessionInterface $session, LivreRepository $repo): Response
    {
        $panier = $session->get('panier', []);
        $dataPanier =[];
        $total = 0;

        foreach ($panier as $id => $quantite) {
            $livre =$repo->find($id);
            $dataPanier[]=
            [
                "livre"=>$livre,
                "quantite"=> $quantite
            ];
            $total += $livre->getPrix()*$quantite;
        }
        return $this->render('panier/panier.html.twig', [
            'dataPanier'=> $dataPanier,
            'total'=> $total
        ]);
    }

    /**
     * @Route("/add/{id<\d+>}", name="add")
     */
    public function add($id, SessionInterface $session , LivreRepository $repo)
    {
        $panier = $session->get('panier', []);

        if (empty($panier[$id])) {
           $panier[$id] = 1; 
        }else{
            $panier[$id]++; 
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute("panier_show");

    }

    /**
     * @Route("/remove/{id<\d+>}", name="remove_livre")
     */
    public function remove($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            if ($panier[$id]>1) {
               $panier[$id]--;
            }else{
                unset($panier[$id]);
            }
        }else{
            $panier[$id]=1; 
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute("panier_show");

    }


    /**
     * @Route("/delete/{id<\d+>}", name="delete_livre")
     */
    public function delete($id, SessionInterface $session) : Response
    {

        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }else{
            $this->addFlash("error", "Le livre que vous essayez de retirer du panier n'existe pas!!!");

            return $this->redirectToRoute("panier_show");
        }

        $session->set("panier", $panier);
        $this->addFlash("success", "Le livre a bien été retiré du panier.");

        return $this->redirectToRoute("panier_show");
    } 

}
