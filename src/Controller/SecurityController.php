<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\templates\blog;
use App\Form\PasswordEditType;
use App\Form\RegistrationType;
use App\Form\EmailPasswordType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

//14.-------------------------Login: Ajouter le Use ----------------------------------
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
//14.-----------------------------Se rendre dans Entity/User-------------------------------------

class SecurityController extends AbstractController
{
    /**
     * @Route("/security", name="security")
     */
    public function index()
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }
    
    
    /**
     * @Route("/login", name="login")
     */    
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // ATTENTION
        // CETTE METHODE EST ACTIVEE
        // SEULEMENT SI ON ARRIVE SUR LA PAGE DIRECTEMENT
        // OU SI ON A RATE LE TRAITEMENT DU FORMULAIRE
        // => SI ON A REUSSI LE LOGIN
        // CETTE METHODE NE SERA PAS ACTIVEE...
        // exemple: SI ON VEUT REDIRIGER VERS UNE PAGE DIFFERENTE SUIVANT LE 	ROLE
        // IL NE FAUT PAS METTRE LE CODE DE REDIRECTION ICI...
	    // EN CAS D'ECHEC SUR LE FORMULAIRE...
        
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
    
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
    
        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }
    
    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        return $this->render('security/logout.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }


    /**
     * @Route("/inscription", name="security_registration")
     */
     public function registration(Request $request, ObjectManager $manager, \Swift_Mailer $mailer)
     {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // Génération d'une clé aléatoire
            $cle = md5(microtime(TRUE)*100000);

            // //Hachage du mot de passe
            $password = $user->getPassword();                               // http://php.net/manual/fr/function.password-hash.php
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);      // ATTENTION: EN BCRYPT...
            $user->setPassword($passwordHash);

            //Attribution d'un role par temporaire
            $user->setRole('ROLE_INACTIVE');

            //Stockage de la clé en base de données
            $user->setCle($cle);

            $manager->persist($user);
            $manager->flush();

            //
            $username = $form['username']->getData();
            $email = $form['email']->getData();
//---------------------------------------------------------------------------------------------------------------------------------------
            // Génération du lien d'activation
            $lienActivation = 'http://localhost/cuisinedessud/public/activation?log='.urlencode($username).'&cle='.$cle;  


            //Envoi du mail de confirmation d'inscription contenant le lien d'activation
            $mail = (new \Swift_Message('MonBlogCuisine'))
            ->setFrom('jpf2b1@gmail.com')
            ->setTo($email)
            ->setBody($this->renderView('security/registrationmail.html.twig',array(
                'username' => $username, 
                'email' => $email,
                'lienActivation'=>$lienActivation, 
                'cle' =>$cle )),'text/html');

            $mailer->send($mail);
//---------------------------------------------------------------------------------------------------------------------------------------
            return $this->redirectToRoute('attente-activation');
        }


         return $this->render('security/registration.html.twig',[
             'form'=> $form->createView()
         ]);
     }
     

    /**
     * @Route("/activation", name="activation")
     */
     public function activation(Request $request, UserRepository $userRepository, ObjectManager $manager)
     {
         $login = $_GET['log'];
         $cleClient  = $_GET['cle'];
 
        //  $clebd = $this->getDoctrine()
        //                 ->getRepository(User::class)
        //                 ->findOneBy(['cle'=>$cleClient]);

        $clebd = $userRepository->findOneBy(['cle'=>$cleClient]);

        

        $role = $clebd ->getRole();
        dump($role);

 
         //Si la clé n'existe pas...
         if(!$clebd){
            echo 'if 1 Si la clé n existe pas';
             return $this->render('security/errorActivation.html.twig');
             
         }
 
         $role = $clebd ->getRole();
         dump($role);
         

         //Si aucun role de defini...
         if($role == NULL){
            echo 'if 2 aucun role defini';
             return $this->render('security/errorActivation.html.twig');
             
         }
         
         //Si le role est deja en "member"...
         if($role == 'ROLE_MEMBER')
         {
            echo 'if 3 role deja a member';
             return $this->render('security/alreadyActivat.html.twig');
         }
 
         //Si le role a le statut "ROLE_INACTIVE"...
         if ($role == 'ROLE_INACTIVE' )
         {
            echo 'if 4 role à inactive';
             $role = $clebd ->setRole('ROLE_MEMBER');
 
             $manager->persist($clebd);
             $manager->flush();
 
             return $this->render('security/activation.html.twig');
         }
         
        //  if($role !== 'ROLE_INACTIVE' or 'ROLE_MEMBER')
        //  {
        //      return $this->render('security/errorActivation.html.twig');
        //  }

        if(!in_array($role, ['ROLE_INACTIVE', 'ROLE_MEMBER']))
         {
            dump ($role);
            echo 'if 5 role different de inactiv ou member';
             return $this->render('security/errorActivation.html.twig');
             
         }
     }

    /**
     * @Route("/attente-activation", name="attente-activation")
     */
    public function attenteActivation()
    {
        return $this->render('security/attenteActivation.html.twig'); 
    }

    /**
    * @Route("/password-revovery", name="password-revovery")
    */
    public function passwordRevovery(Request $request, ObjectManager $manager, \Swift_Mailer $mailer)
    {
        $User = new User();
        $form = $this->createForm(EmailPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $emailForm = $form['email']->getData();
        
            $email = $this->getDoctrine()
                           ->getRepository(User::class)
                           ->findOneBy(['email'=> $emailForm]);
        // dump($email);
        // dump($emailForm);
        // echo "OK";

        if(!$email){
        return $this->render('security/errorEmail.html.twig');
        }

        $cle = md5(microtime(TRUE)*100000);
        $email->setCle($cle);

        $manager->persist($email);
        $manager->flush();

        //Construction et Envoi du mail contenant le lien de modif du mot de passe  
        $emailform = $form['email']->getData();
        //dump($emailform);

        $mail = (new \Swift_Message('Mon BlogCuisine'))
                ->setFrom('jpf2b1@gmail.com')
                ->setTo($emailform)
                ->setBody($this->renderView('security/recoveryMail.html.twig',array('cle' =>$cle )),'text/html');

                $mailer->send($mail);

                return $this->render('security/emailOk.html.twig',['email'=>$emailform]);

        }

    return $this->render('security/passwordRevovery.html.twig',[
            'form'=>$form->createView()
        ]);  
    }


    /**
    * @Route("/passwordEdit", name="passwordEdit")
    */
    public function editpassword(Request $request, ObjectManager $manager){

        //On crée le formulaire de changement de mot de passe
        $user = new User();
        $form =$this->createForm(PasswordEditType::class, $user);
        $form->handleRequest($request);

        $cleClient  = $_GET['cle'];
        $password = $form['password']->getData();

        //trouver le password en base de données grace à la clé client
        $newpassword = $this->getDoctrine()
        ->getRepository(User::class)
        ->findOneBy(['cle'=>$cleClient]);

        if(!$newpassword){
            return $this->render('security/errorEditPassword.html.twig');
        }
        if($form->isSubmitted() && $form->isValid()){

            //On modifie la clé en base de donnée par du vide se qui rend le lien de modif de mot de passe à usage unique
            $newpassword->setCle('');
           
            $manager->persist($newpassword);
            $manager->flush();

            //On hash le nouveau password (saisi ds le formulaire)
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            //On le remplace en base de données
            $newpassword->setPassword($passwordHash);

            //Persit et flush
            $em = $this->getDoctrine()->getManager();
            $em->persist($newpassword);
            $em->flush();
            
            return $this->render('security/sucssessEditPassword.html.twig');
        }
           


        return $this->render('security/passwordEdit.html.twig',[
            'form'=>$form->createView()
        ]);

    }

    

}

