<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Entity\Quantite;
use App\Form\RecetteType;
use App\Entity\Ingredient;
use App\Repository\RecetteRepository;
use App\Repository\QuantiteRepository;
use App\Repository\IngredientRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

use Symfony\Component\HttpFoundation\Response;



//-------------------------02 table relation recette  user:-------------------------------
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//--------------------------------------------------------------------------------------------

/**
 * @Route("/recette")
 */
class RecetteController extends Controller
{
    /**
     * @Route("/", name="recette_index", methods="GET")
     */
    public function index(RecetteRepository $recetteRepository, QuantiteRepository $quantiteRepository, IngredientRepository $ingredientRepository): Response
    {
        return $this->render('recette/index.html.twig', [
            'recettes' => $recetteRepository->findAll(), 
            'quantites'=>$quantiteRepository->findAll(),
            'ingredients'=>$ingredientRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="recette_new", methods="GET|POST")
     */
    public function new(Request $request, IngredientRepository $ingredientRepository): Response
    {

        $em = $this->getDoctrine()->getManager();

        $recette = new Recette();
        $user = $this->getUser();
        
        $form = $this->createForm(RecetteType::class, $recette, ['user'=>$user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {                   
//-------------------------01 table relation recette et user :-------------------------------         
           if(!(in_array("ROLE_ADMIN", $user->getRole())))
            {
                $recette->setAuteur($user);
            }   
//--------------------------------------------------------------------------------------------

            $file = $recette->getImage();
            $fileName=$file->getClientOriginalName();


            $file->move(
            $this->getParameter('upload_directory'),
            $fileName
            );
            
            $recette->setImage($fileName);            
            
            $em->persist($recette);
            $em->flush();

          
            
            foreach($_REQUEST["ingredient"] as $cle => $nom){
                $quantite = new Quantite;

                $quantite->setRecette($recette);
                $quantite->setNombre($_REQUEST["quantite"][$cle]);
                $quantite->setUnite($_REQUEST["unite"][$cle]);

                $ingredient = $ingredientRepository->findOneByNom($nom);
                if($ingredient == null){
                    $ingredient = new Ingredient;
                    $ingredient->setNom($nom);
                    $em->persist($ingredient);
                }

                //$em->persist($ingredient);

                $quantite->setIngredient($ingredient);



                $em->persist($quantite);
                $em->flush();
            }

            return $this->redirectToRoute('recette_index');
        }

        return $this->render('recette/new.html.twig', [
            'recette' => $recette,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="recette_show", methods="GET")
     */
    public function show(Recette $recette): Response
    {
        return $this->render('recette/show.html.twig', ['recette' => $recette]);
    }

    /**
     * @Route("/{id}/edit", name="recette_edit", methods="GET|POST")
     */
    public function edit(Request $request, Recette $recette): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(RecetteType::class, $recette,['user'=>$user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();



            foreach($_REQUEST["ingredient"] as $cle => $nom){
                $quantite = new Quantite;

                $quantite->setRecette($recette);
                $quantite->setNombre($_REQUEST["quantite"][$cle]);
                $quantite->setUnite($_REQUEST["unite"][$cle]);

                $ingredient = $ingredientRepository->findOneByNom($nom);
                if($ingredient == null){
                    $ingredient = new Ingredient;
                    $ingredient->setNom($nom);
                    $em->persist($ingredient);
                }

                //$em->persist($ingredient);

                $quantite->setIngredient($ingredient);



                $em->persist($quantite);
                $em->flush();
            }



            
            $file = $recette->getImage();
            $fileName=$file->getClientOriginalName();

            
            $file->move(
            $this->getParameter('upload_directory'),
            $fileName
            );
            
            $recette->setImage($fileName);            
            
            
            
            $em->persist($recette);
            $em->flush();

            return $this->redirectToRoute('recette_index');
        }

        return $this->render('recette/edit.html.twig', [
            'recette' => $recette,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="recette_delete", methods="DELETE")
     */
    public function delete(Request $request, Recette $recette): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recette->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($recette);
            $em->flush();
        }

        return $this->redirectToRoute('recette_index');
    }
}
