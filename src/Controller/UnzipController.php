<?php

namespace App\Controller;

use App\Entity\Ecole;
use App\Entity\Ecole\Classe;
use App\Entity\Ecole\Enseignant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class UnzipController extends AbstractController
{
	/**
	 * @Route("/unzip_ajax", name="unzip_ajax", methods={"POST"})
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function UnzipAjaxAction(Request $request)
	{
		if ($request->isXMLHttpRequest()) {

			$iFile = $request->files->get('file');

			$zip = new \ZipArchive;
			$res = $zip->open($iFile);
			if ($res === TRUE) {
				$zip->extractTo('temp/');
				$zip->close();

				$jsonFile = json_decode(file_get_contents('temp/descriptif.json'));

				$manager = $this->getDoctrine()->getManager();

				foreach ($jsonFile->ecoles as $iEcole) {
					// On met en place l'école
					$ecole = new Ecole();
					$ecole->setNom($iEcole->nom);
					$ecole->setVille($iEcole->ville);
					$ecole->setVignetteName($iEcole->fichierImage);
					$manager->persist($ecole);

					$ecole->setVignetteFile(new UploadedFile('temp/'.$iEcole->fichierImage, $iEcole->fichierImage, null, null, true), false);
					copy('temp/'.$iEcole->fichierImage, 'medias/images/'.$iEcole->fichierImage);

					$position = 0;

					foreach($iEcole->classes as $iClasse) {
						// On met en place les classes de l'école
						$classe = new Classe();
						$classe->setEcole($ecole);
						$classe->setPosition($position);
						$classe->setTitre($iClasse->titre);
						$classe->setNiveau($iClasse->niveau);
						$classe->setVignetteName($iClasse->fichierImage);

						foreach($iClasse->enseignants as $iEnseignant) {
							// On met en place les enseignants de la classe
							$enseignant = new Enseignant();
							$enseignant->setIdentite($iEnseignant->identite);
							$enseignant->setVignetteName($iEnseignant->fichierImage);

							$manager->persist($enseignant);

							$classe->addEnseignant($enseignant); // On lit l'enseignant à la classe
						}

						$manager->persist($classe);
					}
				}

				$manager->flush();

				array_map('unlink', glob('temp/*')); // On vide le contenu du répertoire temporaire

				return new JsonResponse(
					[
						"success" => 'Votre fichier JSON a été importé correctement ainsi que toutes les images associées.'
					]
				);
			}
			else {
				return new JsonResponse(
					[
						"error" => 'Votre fichier n\'est pas un ZIP'
					]
				);
			}
		}

		return new JsonResponse('This is not ajax!', 400);
	}
}
