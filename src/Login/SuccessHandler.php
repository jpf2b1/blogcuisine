<?php

namespace App\Login;

//17.--0/3-----Login: 16. ## REDIRECTION APRES LOGIN (SUIVANT ROLE DU VISITEUR)--------------
//Creer dossier Login ds src
//creer le fichier SuccessHandler.php
//CREER UNE CLASSE QUI IMPLEMENTE AuthenticationSuccessHandlerInterface
//AJOUTER UN PARAMETE success_handler DANS LE FICHIER security.yaml
//ON DONNE LE NOM DE LA CLASSE A ACTIVER
//ET AJOUTER LA METHODE POUR GERER LA REDIRECTION APRES LOGIN...
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

//17.3/3------Login: Ajouter les Uses : AccessDeniedHandlerInterface et Response
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\HttpFoundation\Response;
//17.3/3-----------------------FIN DU LOGIN-----------------------------------------------

class SuccessHandler 
    implements AuthenticationSuccessHandlerInterface, AccessDeniedHandlerInterface //17.1/3------Login: AJOUTER UN CODE SUR LE 403 Access Denied:* CREER UNE CLASSE QUI IMPLEMENTE AccessDeniedHandlerInterface
{                                                                                  //17.-------Se rendre ds security.yaml---------------------
    // PROPRIETE POUR MEMORISER LE ROUTER
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }
    
    public function onAuthenticationSuccess($request, $token)
    {
        if (in_array('ROLE_ADMIN', $token->getUser()->getRoles())) {
            // JE PEUX UTILISER LE ROUTEUR MEMORISE
            return new RedirectResponse($this->router->generate('admin'));
        }
        if (in_array('ROLE_USER', $token->getUser()->getRoles())) {
            // JE PEUX UTILISER LE ROUTEUR MEMORISE
            return new RedirectResponse($this->router->generate('home'));
        }
        else {
            return new RedirectResponse($this->router->generate('login'));
        }
    }
    
    
    public function handle($request, $accessDeniedException)
    {
        // ...

        return new Response("<body>INTERDIT DESOLE...</body>", 403);
    }
}
