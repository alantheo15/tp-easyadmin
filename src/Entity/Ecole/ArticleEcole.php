<?php

namespace App\Entity\Ecole;

use App\Entity\Ecole\Classe;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * ArticleEcole
 *
 * @ORM\Table(name="articleecole")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 *
 */
class ArticleEcole
{
    const SERVER_PATH_TO_IMG_FOLDER = 'medias/images';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="text")
     */
    protected $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

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
     * @var Boolean
     *
     * @ORM\Column(name="visible", type="boolean")
     */
    private $visible = false;

    /**
     * image de l'article
     * @var UploadedFile
     * @Vich\UploadableField(mapping="vignetteFile", fileNameProperty="vignetteFile")
     */
    private $vignetteFile;

    /**
     * @var string
     *
     * @ORM\Column(name="vignetteName", type="string", length=255, unique=false, nullable=true)
     */
    private $vignetteName;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ecole\Classe", inversedBy="articles")
     * @ORM\JoinColumn(name="classe_id", referencedColumnName="id")
     */
    private $classe;

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
        return (string) $this->getTitre();
    }

    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return ArticleEcole
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return ArticleEcole
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * @return ArticleEcole
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
     * @return ArticleEcole
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

    public function refreshUpdated()
    {
        $this->setUpdatedAt(new DateTime());
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
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
     * Set classe
     *
     * @param Classe $classe
     *
     * @return ArticleEcole
     */
    public function setClasse($classe)
    {
        $this->classe = $classe;

        return $this;
    }

    /**
     * Get classe
     *
     * @return Classe
     */
    public function getClasse()
    {
        return $this->classe;
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
