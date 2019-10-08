<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Auteur;

class AuteursFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
       
        
        for($i= 1; $i<= 10; $i++){
            $auteur = new Auteur();
            $auteur ->setPseudo("prenom".$i)
                    ->setActif(true);
                    
                $manager->persist($auteur);
                
        }

        $manager->flush();
    }
}
