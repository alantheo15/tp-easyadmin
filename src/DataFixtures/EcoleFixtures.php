<?php

namespace App\DataFixtures;

use App\Entity\Ecole;
use App\Entity\Ecole\Classe;
use App\Entity\Ecole\Enseignant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Exception;

class EcoleFixtures extends Fixture implements OrderedFixtureInterface
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager)
    {
		$ecole = new Ecole();
		$ecole->setNom("IUT");
		$ecole->setVille("Laval");
		$ecole->setVignetteName("PS.png");
		$manager->persist($ecole);
		$manager->flush();

		try {
			$ecole->setVignetteFile(new UploadedFile('public/medias/fixtures/PS.png', 'PS.png', null, null, true), false);
			$ecole->uploadVignetteImg(true);
		} catch (\Exception $e) {
		}


		$this->loadClasse($manager,
            "PS",
            0,
            "maternelle",
            'PS.png',
            'user1',
			$ecole
        );
        $this->loadClasse($manager,
            "MS",
            1,
            "maternelle",
            "MS.png",
            'user2',
			$ecole
        );
        $this->loadClasse($manager,
            "GS",
            2,
            "maternelle",
            "GS.png",
            'user3',
			$ecole
        );
        $this->loadClasse($manager,
            "CP",
            3,
            "primaire",
            "CP.jpg",
            'user4',
			$ecole
        );
        $this->loadClasse($manager,
            "CE1",
            4,
            "primaire",
            "CE1.jpg",
            'user5',
			$ecole
        );
        $this->loadClasse($manager,
            "CE2",
            5,
            "primaire",
            "CE2.jpg",
            'user6',
			$ecole
        );
        $this->loadClasse($manager,
            "CM1",
            6,
            "primaire",
            "CM1.jpg",
            'user7',
			$ecole
        );
        $this->loadClasse($manager,
            "CM2",
            7,
            "primaire",
            "CM2.jpg",
            'user8',
			$ecole
        );

        $manager->flush();
    }

    public function loadClasse($manager, $titre, $position, $niveau, $img, $userRef, $ecole){
        $classe = new Classe();
        $classe->setTitre($titre);
        $classe->setPosition($position);
        $classe->setNiveau($niveau);
        $classe->setEcole($ecole);
        $classe->addEnseignant($this->getReference("user_".$userRef));
        $manager->persist($classe);
        $manager->flush();

        if($img !== ''){
            try {
                $classe->setVignetteFile(new UploadedFile('public/medias/fixtures/' . $img, $img, null, null, true), false);
                $classe->uploadVignetteImg(true);
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * Get the order of this fixture
     *
     * @return int
     */
    public function getOrder()
    {
        // TODO: Implement getOrder() method.
        return 10;
    }
}
