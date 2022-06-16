<?php

namespace App\Controller;

use App\Service\FamilleService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FamilleController extends AbstractController
{
    /**
     * @Route("/", name="app_famille")
     */
    public function index(FamilleService $familleServile, Request $request): Response
    {
        $familles          = [];
        $famille           = "";
        $sous_famille      = "";
        $message_erreur    = "";
        $sous_sous_famille = "";

        // Validation du champs
        if($request->request->count()) : 
            //  Récuperation de la valeur saisie
            $champs = $request->request->get("sous_sous_famille");
            // Utilisation du service famille pour recuperer les infos
            $familles = $familleServile->recupererSousFamille($champs);
            if(count($familles) > 0) : 
                $famille           = $familles['Famille'];
                $sous_famille      = $familles['Sous Famille'];
                $sous_sous_famille = $champs;
            else : 
                $message_erreur    = "La sous sous famille saisie n'existe pas !";
            endif;

        endif;
        return $this->render('famille/index.html.twig', [
            'familles'          => $familles,
            'famille'           => $famille,
            'sous_famille'      => $sous_famille,
            'message_erreur'    => $message_erreur,
            'sous_sous_famille' => $sous_sous_famille,
        ]);
    }
}
