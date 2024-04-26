<?php

namespace App\Entity;

use App\Repository\EspecialidadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EspecialidadRepository::class)]
class Especialidad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $descripción = null;

    // Añadimos la colección de médicos asociados a esta especialidad
    #[ORM\OneToMany(targetEntity: Medico::class, mappedBy: 'especialidad')]
    private Collection $medicos;

    public function __construct()
    {
        $this->medicos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getDescripción(): ?string
    {
        return $this->descripción;
    }

    public function setDescripción(string $descripción): self
    {
        $this->descripción = $descripción;
        return $this;
    }

    // Métodos para acceder y modificar la colección de médicos
    public function getMedicos(): Collection
    {
        return $this->medicos;
    }

    public function addMedico(Medico $medico): self
    {
        if (!$this->medicos->contains($medico)) {
            $this->medicos[] = $medico;
            $medico->setEspecialidad($this);
        }
        return $this;
    }

    public function removeMedico(Medico $medico): self
    {
        if ($this->medicos->removeElement($medico)) {
            // Set the owning side to null (unless already changed)
            if ($medico->getEspecialidad() === $this) {
                $medico->setEspecialidad(null);
            }
        }
        return $this;
    }
}

