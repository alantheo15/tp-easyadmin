<?php

namespace App\DataFixtures;

use App\Entity\Ecole\Classe;
use App\Entity\Ecole\Enseignant;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Exception;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $password = "test";
        $admin->setEmail("admin@test.com");
        $admin->setPlainPassword($password);
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            $password
        ));
        $manager->persist($admin);
        $manager->flush();


        $this->loadUser($manager,
            "Mme Michu",
            true,
            "user1",
            "woman1.png"
            );
        $this->loadUser($manager,
            "Mme Bidule",
            true,
            "user2",
            "woman2.png"
        );
        $this->loadUser($manager,
            "Mr Truc",
            true,
            "user3",
            "man1.png"
        );
        $this->loadUser($manager,
            "Mr ZZtop",
            true,
            "user4",
            "man2.png"
        );
        $this->loadUser($manager,
            "Mr Genuflexion",
            true,
            "user5",
            "man3.png"
        );
        $this->loadUser($manager,
            "Mr Z",
            true,
            "user6",
            "man4.png"
        );
        $this->loadUser($manager,
            "Mme Knock",
            true,
            "user7",
            "woman3.png"
        );
        $this->loadUser($manager,
            "Mme Lajoie",
            true,
            "user8",
            "woman4.png"
        );
        $manager->flush();
    }

    public function loadUser($manager, $identite, $visible, $ref, $img){
        $user = new Enseignant();
        $user->setIdentite($identite);
        $user->setVisible($visible);
        $manager->persist($user);
        $manager->flush();
        $this->addReference("user_".$ref, $user);

        if($img !== ''){
            try {
                $user->setVignetteFile(new UploadedFile('public/medias/fixtures/' . $img, $img, null, null, true), false);
                $user->uploadVignetteImg(true);
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
        return 1;
    }
}
