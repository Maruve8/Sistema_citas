<?php

namespace App\Entity;

use App\Repository\MedicoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MedicoRepository::class)]
class Medico
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $apellidos = null;

    // Aquí se define correctamente la relación con Especialidad.
    #[ORM\ManyToOne(targetEntity: Especialidad::class, inversedBy: 'medicos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Especialidad $especialidad = null;

    // Relación con Citas
    #[ORM\OneToMany(targetEntity: Cita::class, mappedBy: 'médico', orphanRemoval: true)]
    private Collection $citas;

    public function __construct()
    {
        $this->citas = new ArrayCollection();
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

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): self
    {
        $this->apellidos = $apellidos;
        return $this;
    }

    // Actualizado para devolver una instancia de Especialidad en lugar de string
    public function getEspecialidad(): ?Especialidad
    {
        return $this->especialidad;
    }

    // Actualizado para aceptar una instancia de Especialidad en lugar de string
    public function setEspecialidad(?Especialidad $especialidad): self
    {
        $this->especialidad = $especialidad;
        return $this;
    }

    // Métodos para gestionar las citas asociadas con el médico
    public function getCitas(): Collection
    {
        return $this->citas;
    }

    public function addCita(Cita $cita): self
    {
        if (!$this->citas->contains($cita)) {
            $this->citas->add($cita);
            $cita->setMédico($this);
        }

        return $this;
    }

    public function removeCita(Cita $cita): self
    {
        if ($this->citas->removeElement($cita)) {
            // Set the owning side to null (unless already changed)
            if ($cita->getMédico() === $this) {
                $cita->setMédico(null);
            }
        }

        return $this;
    }
}

