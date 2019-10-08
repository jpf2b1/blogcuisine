<?php

namespace App\Controller;
use App\Entity\Blog;
use App\Entity\Contact;
use App\Entity\Recette;
use App\Entity\Quantite;
use App\Entity\Ingredient;
use App\Form\PageContactType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     * @Route("/", name="home")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(Recette::class);
        $recettes = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'recettes' => $recettes
        ]);
    }
    
    // /**
    // *@Route("/", name="home")
    // */
    // public function home()
    // {
    //     return $this->render('blog/home.html.twig');
    // }


    /**
    *@Route("/blog/{id}", name="blog_show")
    */
    public function show($id){
        $repo = $this->getDoctrine()->getRepository(Recette::class);
        $recette = $repo->find($id);

        $repo2 = $this->getDoctrine()->getRepository(Ingredient::class);
        $ingredient = $repo2->find($id);

        $repo3 = $this->getDoctrine()->getRepository(Quantite::class);
        $quantite = $repo3->find($id);


        return $this->render('blog/show.html.twig', ['recette' => $recette, 'ingredient' => $ingredient, 'quantite' => $quantite]);
    }


    /**
     * @Route("/contact", name="contact")
     */
    public function createContact(Request $request, ObjectManager $manager, \Swift_Mailer $mailer)
    {
        $contact = new Contact;   
  
        
        $form = $this->createForm(PageContactType::class, $contact);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {   


            $name = $form['name']->getData();
            $email = $form['email']->getData();
            $subject = $form['subject']->getData();
            $message = $form['message']->getData();

            # set form data 
            $contact->setName($name);
            $contact->setEmail($email);          
            $contact->setSubject($subject);     
            $contact->setMessage($message);
            
            # finally add data in database
            $sn = $this->getDoctrine()->getManager();      
            $sn -> persist($contact);
            $sn -> flush();


            $mail = (new \Swift_Message('Hello Email'))
            ->setFrom('jpf2b1@gmail.com')
            ->setTo('jpf2b1@gmail.com')
            ->setBody($this->renderView(
                    // templates/emails/registration.html.twig
                    'blog/registrationmail.html.twig',
                    array('name' => $name)
                ),
                'text/html'
            );




            $mailer->send($mail);
            return $this->redirectToRoute('home');

        }

        return $this->render('Blog/pageContact.html.twig',[
            'form'=> $form->createView()
        ]);
    }
}
