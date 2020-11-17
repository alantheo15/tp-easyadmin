<?php


namespace App\Controller\Admin;

use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ZipArchive;

class AdminCrudController extends AbstractController
{

    /**
     * @Route("admin/uploadEcole", name="uploadEcole", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function ImportActions(Request $request): Response
    {
        $flashbag = $this->get( 'session' )->getFlashBag();

        return $this->render( "admin/uploadEcole.html.twig",
            [
                'ecoles' => []
            ]
        );
    }
}
