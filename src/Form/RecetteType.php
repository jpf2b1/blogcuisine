<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Recette;
use App\Entity\Quantite;
use App\Entity\Ingredient;

use App\Form\IngredientType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Security\Core\Security;

//-------------------------05 table relation recette et user :-------------------------------
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
//-------------------------------------------------------------------------------------------


//-------------------------06 table relation recette et user :-------------------------------
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
//-------------------------------------------------------------------------------------------



class RecetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//-------------------------04 table relation recette et user :-------------------------------
        // $security=new Security;
        
//-------------------------------------------------------------------------------------------
        $builder
            ->add('titre')
            // ->add('quantite', EntityType::class,[ 'class' => Quantite::class, 'choice_label' => 'ingredient.nom'])
            // ->add('quantite', EntityType::class,[ 'class' => Quantite::class, 'choice_label' => 'unite'])
            // ->add('quantite', EntityType::class,[ 'class' => Quantite::class, 'choice_label' => 'nombre'])

            ->add('tempsPreparation')
            ->add('preparation')
            // ->add('image')

            // ->add('auteur', EntityType::class, array(
            //     'class' => User::class,
            //     'choice_label' => 'username',))

            ->add('FichierImage', FileType::class, array('label' => 'Image','required'=>false))
            ->add('categorie')

            // ->add('ingredients', CollectionType::class, array(
            //     'entry_type'   => IngredientType::class,
            //     'allow_add'    => true,
            //     'allow_delete' => true))

        ;
//-------------------------03 table relation recette et user :-------------------------------
// $userLogger = $security->getUser();
$userLogger = $options['user'];
if(in_array("ROLE_ADMIN", $userLogger->getRoles())){    
    $builder->add('auteur', EntityType::class, array('class' => User::class,'choice_label' => 'username',));
}
//-------------------------------------------------------------------------------------------

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
            'user' => null,
        ]);
    }
}
