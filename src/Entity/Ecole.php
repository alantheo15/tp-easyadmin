<?php

namespace App\Entity;

use App\Entity\Ecole\Classe;
use App\Repository\EcoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=EcoleRepository::class)
 * @Vich\Uploadable
 */
class Ecole
{
	const SERVER_PATH_TO_IMG_FOLDER = 'medias/images';

	/**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ville;

	/**
	 * image de l'ecole
	 * @var UploadedFile
	 * @Vich\UploadableField(mapping="vignetteFile", fileNameProperty="vignetteName")
	 */
	private $vignetteFile;

	/**
     * @ORM\Column(type="string", length=255)
     */
    private $vignetteName;

    /**
     * @ORM\OneToMany(targetEntity=Classe::class, mappedBy="ecole")
     */
    private $classes;

    public function __construct()
    {
        $this->classes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

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

    public function getVignetteName(): ?string
    {
        return $this->vignetteName;
    }

    public function setVignetteName(string $vignetteName): self
    {
        $this->vignetteName = $vignetteName;

        return $this;
    }

    /**
     * @return Collection|Classe[]
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(Classe $class): self
    {
        if (!$this->classes->contains($class)) {
            $this->classes[] = $class;
            $class->setEcole($this);
        }

        return $this;
    }

    public function removeClass(Classe $class): self
    {
        if ($this->classes->removeElement($class)) {
            // set the owning side to null (unless already changed)
            if ($class->getEcole() === $this) {
                $class->setEcole(null);
            }
        }

        return $this;
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
}
