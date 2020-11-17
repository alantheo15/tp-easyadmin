<?php

namespace App\Controller;

use App\Entity\Ecole\Classe;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class EcoleController extends AbstractController
{
    /**
     * Ecole
     * @Route("/ecole", name="ecole", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function ecoleAction(Request $request){
        /**
         * @var $em EntityManager
         */
        $em = $this->getDoctrine()->getManager();
        $classes = $em->getRepository(Classe::class)->findAll();

        return $this->render("ecole.html.twig", [
            'classes' => $classes,
        ]);
    }

    /**
     * Ecole
     * @Route("/ecole/articles", name="ecoleArticles", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function ecoleArticlesAction(Request $request){
        /**
         * @var $em EntityManager
         */
        $em = $this->getDoctrine()->getManager();
        $classes = $em->getRepository(Classe::class)->findAll();

        return $this->render("ecole.html.twig", [
            'classes' => $classes
        ]);
    }
}