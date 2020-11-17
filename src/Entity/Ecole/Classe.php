<?php

namespace App\Entity\Ecole;

use App\Entity\Ecole;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Classe
 *
 * @ORM\Entity
 * @ORM\Table(name="classe")
 *
 * @ORM\HasLifecycleCallbacks
 */
class Classe
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
     * @ORM\Column(name="titre", type="string", length=1024)
     */
    protected $titre;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(name="niveau", type="string", length=1024)
     */
    private $niveau;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ArticleEcole", mappedBy="classe")
     */
    private $articles;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Enseignant", mappedBy="classes")
     */
    private $enseignants;

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
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true, length=10000)
    */
    private $description;

    /**
     * image de la classe
     * @var UploadedFile
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

    /**
     * @ORM\ManyToOne(targetEntity=Ecole::class, inversedBy="classes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ecole;

    public function __construct() {
        $this->articles = new ArrayCollection();
        $this->enseignants = new ArrayCollection();
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
        return (string) $this->getTitre();
    }

    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return Classe
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
     * Set niveau
     *
     * @param string $niveau
     *
     * @return Classe
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * Get niveau
     *
     * @return string
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Classe
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
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
     * @return Classe
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
     * @return Classe
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
     * Set articles
     *
     * @param array|ArrayCollection $articles
     *
     * @return Classe
     */
    public function setArticles($articles)
    {
        $this->articles = $articles;

        return $this;
    }

    /**
     * Get articles
     *
     * @return ArrayCollection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Add article
     *
     * @param ArticleEcole $article
     *
     * @return Classe
     */
    public function addArticle($article)
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setClasse($this);
        }
        return $this;
    }

    /**
     * Remove article
     *
     * @param ArticleEcole $article
     *
     * @return Classe
     */
    public function removeArticle($article)
    {
        $this->articles->removeElement($article);

        return $this;
    }

    /**
     * Set enseignants
     *
     * @param array $enseignants
     *
     * @return Classe
     */
    public function setEnseignants($enseignants)
    {
        $this->enseignants = $enseignants;

        return $this;
    }

    /**
     * Get enseignants
     *
     * @return ArrayCollection
     */
    public function getEnseignants()
    {
        return $this->enseignants;
    }

    /**
     * Add enseignant
     *
     * @param Enseignant $enseignant
     *
     * @return Classe
     */
    public function addEnseignant($enseignant)
    {
        if(!$this->enseignants->contains($enseignant)){
            $this->enseignants[] = $enseignant;
            $enseignant->addClasse($this);
        }

        return $this;
    }

    /**
     * Remove enseignant
     *
     * @param Enseignant $enseignant
     */
    public function removeEnseignant($enseignant)
    {
        $this->enseignants->removeElement($enseignant);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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

    public function getEcole(): ?Ecole
    {
        return $this->ecole;
    }

    public function setEcole(?Ecole $ecole): self
    {
        $this->ecole = $ecole;

        return $this;
    }
}