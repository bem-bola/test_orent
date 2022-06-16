<?php

namespace App\Service;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

Class FamilleService {
    //  variable defini dans service yaml
    // Chemin vers le dossier csv
    public $chemin_dossier_csv_directory;

    // Methode magique qui permet de recuperer la valeur 
    // de la variable chemin_dossier_csv_directory definie dans config service yaml
    public function __construct($chemin_dossier_csv_directory) {
        $this->chemin_dossier_csv_directory = $chemin_dossier_csv_directory;
    }
    // Lecture de fichier csv
    public function donneeFichier() {
        // Chemin du fichier famille
        $fichier = $this->chemin_dossier_csv_directory . 'famille.csv';
        
        // Extension du fichier
        $extensionFichier = pathinfo($fichier,PATHINFO_EXTENSION);

        $normalizers = [new ObjectNormalizer()];
        $enconders = [new CsvEncoder()];
        $serializer = new Serializer($normalizers, $enconders);

        /** @var string $fileString*/
        $fileString = file_get_contents($fichier);

        // delimiteur, l'entete
        $options = [
            "csv_headers" => [
                "Famille" , 
                "Sous Famille",
                "Sous Sous Famille"
            ], 
            "csv_delimiter" => ";",
            
        ];
        
        // Les données du fichier famille sous forme d'array
        $donnees = $serializer->decode($fileString, $extensionFichier, $options);

        // Iterartion me permettant de soigner les données recupéré
        // Certaines donneés sont renvoyées avec de valeur. ex: donnees[1] = ["Famille" => "Meuble", "Sous Famille" => "", "Sous sous Famille" => ""]
        // L'iteration me permet de ranger ce genre de données
        foreach ($donnees as $key => $donnee) :
            // La premiere ligne est l'entete
            if ($key != 0) : 
                $famille = $donnees[$key-1]['Famille'];
                $sous_famille = $donnees[$key-1]['Sous Famille'];
                    // var_dump($donnee);
                if ($donnee["Famille"] == "") :
                    $donnees[$key]['Famille'] = $famille;
                endif;
                if ($donnee["Sous Famille"] == "") :
                    $donnees[$key]['Sous Famille'] = $sous_famille;
                endif;
            endif;
        endforeach;

        return $donnees;
    }
    // Methode permettant de recuperer
    public function recupererSousFamille (string $sous_sous_famille): array {
        $donnees = $this->donneeFichier();
        $resultat = [];
        foreach ($donnees as $key => $donnee) :
            if(in_array($sous_sous_famille, $donnee)) : 
                $resultat = $donnee;
            break;
            endif;
        endforeach;
        return $resultat;
    }
}