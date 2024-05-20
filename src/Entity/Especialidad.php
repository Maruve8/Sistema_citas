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

    
    #[ORM\OneToMany(targetEntity: Medico::class, mappedBy: 'especialidad')]
    private Collection $medicos;

    #[ORM\OneToMany(mappedBy: 'especialidad', targetEntity: Cita::class)]
    private Collection $citas;

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
            
            if ($medico->getEspecialidad() === $this) {
                $medico->setEspecialidad(null);
            }
        }
        return $this;
    }

    public function getCitas(): Collection
    {
        return $this->citas;
    }

    public function addCita(Cita $cita): self
    {
        if (!$this->citas->contains($cita)) {
            $this->citas[] = $cita;
            $cita->setEspecialidad($this);
        }
        return $this;
    }

    public function removeCita(Cita $cita): self
    {
        if ($this->citas->removeElement($cita)) {
            
            if ($cita->getEspecialidad() === $this) {
                $cita->setEspecialidad(null);
            }
        }
        return $this;
    }
}

