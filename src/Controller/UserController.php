<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * Affiche la page de connexion
     * @Route("/login",name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function appLoginAction(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        /**
         * Appel doctrine
         */
        $em = $this->getDoctrine();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render("login.html.twig", [
            'title' => "Connexion",
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * Deconnexion
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        return $this->redirectToRoute( 'homepage' );
    }
}
