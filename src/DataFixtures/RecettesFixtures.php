<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Recette;


class RecettesFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        for($i= 1; $i<= 10; $i++){

            // $mn="Minutes";
            // $temps=rand(20, 90);
            
            
            $input = array("Entrée", "Plats", "Dessert");
            $rand_keys = array_rand($input, 2);
            $categorie= $input[$rand_keys[1]];
            
            $recette = new Recette();
            $recette ->setTempsPreparation(\DateTime::createFromFormat('i', "90"))
                     ->setPreparation("Lorem Elsass ipsum Miss Dahlias rucksack varius picon bière ch'ai Oberschaeffolsheim adipiscing hopla habitant libero, Pfourtz ! s'guelt Hans leverwurscht nüdle dui amet gravida Verdammi.

Spätzle eget suspendisse turpis kartoffelsalad risus, tristique messti de Bischheim libero.  Barapli Coopé de Truchtersheim Yo dû. ante quam, hopla lacus ftomi! semper in, et Huguette Pellentesque munster sit Heineken salu tellus sit knepfle Strasbourg ac Christkindelsmärik id aliquam commodo so réchime id, jetz gehts los Chulien und consectetur.

Oberschaeffolsheim rossbolla elementum ornare senectus DNA, pellentesque quam. placerat Gal. 
Leo sagittis hoplageiss bredele flammekueche schnaps vulputate schneck tchao bissame hopla non purus Carola wurscht kuglopf mollis hopla amet, condimentum merci vielmols chambon Salut bisamme ac lotto-owe sed météor Mauris hop knack Gal ! elit mamsell dolor ullamcorper libero, mänele leo Salu bissame sit non.
 
Chulia Roberstau morbi wie gewurztraminer amet gal geht's Racing. auctor, geïz turpis, schpeck yeuh. Richard Schirmeck blottkopf, sed Wurschtsalad kougelhopf Kabinetpapier bissame porta nullam vielmols, tellus rhoncus eleifend ornare Morbi dignissim baeckeoffe")
                     ->setImage("image numero".$i)
                     ->setCategorie($categorie);
                    
            $manager ->persist($recette);
                
        }

        $manager->flush();
    }
}
