<?php

namespace App\Entity\Ecole;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Enseignant
 *
 * @ORM\Entity
 * @ORM\Table(name="enseignant")
 * @Vich\Uploadable
 *
 * @ORM\HasLifecycleCallbacks
 */
class Enseignant
{
    const SERVER_PATH_TO_IMG_FOLDER = 'medias/images';
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="identite", type="string", length=1024)
     */
    protected $identite;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Classe", inversedBy="enseignants")
     * @ORM\JoinTable(name="enseignants_classes")
     */
    private $classes;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable = true)
     */
    private $updatedAt;

    /**
     * image de l'enseignant
     * @var UploadedFile
     * @Vich\UploadableField(mapping="vignetteFile", fileNameProperty="vignetteName")
     */
    private $vignetteFile;

    /**
     * @var string
     *
     * @ORM\Column(name="vignetteName", type="string", length=255, unique=false, nullable=true)
     */
    private $vignetteName;

    /**
     * @var Boolean
     * @ORM\Column(name="isVisible", type="boolean")
    */
    private $visible = false;

    public function __construct() {
        $this->classes = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * __toString method
     */
    public function __toString()
    {
        return (string) $this->getIdentite();
    }

    /**
     * Set identite
     *
     * @param string $identite
     *
     * @return Enseignant
     */
    public function setIdentite($identite)
    {
        $this->identite = $identite;

        return $this;
    }

    /**
     * Get identite
     *
     * @return string
     */
    public function getIdentite()
    {
        return $this->identite;
    }

    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new DateTime("now");
    }

    /**
     * Gets triggered every time on update

     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedAt = new DateTime("now");
    }

    /**
     * Set createdAt
     *
     * @param DateTime $createdAt
     *
     * @return Enseignant
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param DateTime $updatedAt
     *
     * @return Enseignant
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set classes
     *
     * @param array|ArrayCollection $classes
     *
     * @return Enseignant
     */
    public function setClasses($classes)
    {
        $this->classes = $classes;

        return $this;
    }

    /**
     * Get classes
     *
     * @return ArrayCollection
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Add classe
     *
     * @param ClassePhysique $classe
     *
     * @return Enseignant
     */
    public function addClasse($classe)
    {
        if (!$this->classes->contains($classe)) {
            $this->classes->add($classe);
            $classe->addEnseignant($this);
        }
        return $this;
    }

    /**
     * Remove classe
     *
     * @param ClassePhysique $classe
     *
     * @return Enseignant
     */
    public function removeClasse($classe)
    {
        $this->classes->removeElement($classe);

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getVignetteFile()
    {
        return $this->vignetteFile;
    }

    /**
     * @param UploadedFile $vignetteFile
     * @param bool $delete
     * @throws Exception
     */
    public function setVignetteFile($vignetteFile, $delete = true)
    {
        if ($delete) {
            //change le nom ici
            $uniqueName = $this->generateUniqueName( $vignetteFile );
            if ($uniqueName != null) {
                $vignetteFile->move(
                    self::SERVER_PATH_TO_IMG_FOLDER,
                    $uniqueName
                );
            }
            $this->vignetteName = $uniqueName;
            if ($this->vignetteFile instanceof UploadedFile) {
                $this->updatedAt = new DateTime( 'now' );
            }
        }else{
            $this->vignetteFile = $vignetteFile;
        }
    }
    /**
     * @return string
     */
    public function getVignetteName()
    {
        return $this->vignetteName;
    }

    /**
     * @param string $vignetteName
     */
    public function setVignetteName($vignetteName)
    {
        $this->vignetteName = $vignetteName;
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     *
     * @param bool $mustKeepOriginal
     * @throws Exception
     */
    public function uploadVignetteImg($mustKeepOriginal = false)
    {
        // the VignetteFile property can be empty if the field is not required
        if (null === $this->getVignetteFile()) {
            return;
        }
        //verifie si il existe déjà un fichier si oui on le supprime
        if($this->getVignetteName() !== null){
            array_map('unlink', glob(getcwd().'/'.self::SERVER_PATH_TO_IMG_FOLDER.'/'.$this->getId()."/*"));
        }

        // we use the original imagefile name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and target filename as params
        if($mustKeepOriginal){
            $dir = getcwd().'/public/'.self::SERVER_PATH_TO_IMG_FOLDER;
            if (!is_dir($dir)) {
                if (false === @mkdir($dir, 0777, true) && !is_dir($dir)) {
                    throw new FileException(sprintf('Unable to create the "%s" directory', $dir));
                }
            } elseif (!is_writable($dir)) {
                throw new FileException(sprintf('Unable to write in the "%s" directory', $dir));
            }
            copy($this->getVignetteFile()->getRealPath(), getcwd().'/public/'.self::SERVER_PATH_TO_IMG_FOLDER.'/'.$this->getVignetteFile()->getClientOriginalName());
        } else {
            $this->getVignetteFile()->move(
                getcwd() . '/' . self::SERVER_PATH_TO_IMG_FOLDER,
                $this->getVignetteFile()->getClientOriginalName()
            );
        }

        // set the path property to the filename where you've saved the imageFile
        $this->setVignetteName($this->getVignetteFile()->getClientOriginalName());

        // clean up the file property as you won't need it anymore
        $this->setVignetteFile(null, false);
    }

    /**
     * Retourne le chemin de la video
     */
    public function getWebPathImg()
    {
        return '/'.self::SERVER_PATH_TO_IMG_FOLDER.'/'.$this->getVignetteName();
    }

    /**
     * @return Boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @return Boolean
     */
    public function getVisible()
    {
        return $this->visible;
    }
    /**
     * @param Boolean $visible
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }

    /**
     * Génère un nom aléatoire
     * @param File $file
     * @return string
     */
    public function generateUniqueName(File $file): string
    {
        if ($file) {
            return md5( uniqid() ) . "." . $file->guessExtension();
        } else {
            return null;
        }
    }
}