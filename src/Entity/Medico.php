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

    
    #[ORM\Column(length: 10)]
    private ?string $turno = null;


    // Relación con Citas
    #[ORM\OneToMany(targetEntity: Cita::class, mappedBy: 'medico', orphanRemoval: true)]
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


    public function getTurno(): ?string
    {
        return $this->turno;
    }

    public function setTurno(string $turno): self
    {
        if (!in_array($turno, ['manana', 'tarde'])) {
            throw new \InvalidArgumentException("Turno inválido");
        }
        $this->turno = $turno;

        return $this;
    }


    
    public function getCitas(): Collection
    {
        return $this->citas;
    }

    public function addCita(Cita $cita): self
    {
        if (!$this->citas->contains($cita)) {
            $this->citas->add($cita);
            $cita->setMedico($this);
        }

        return $this;
    }

    public function removeCita(Cita $cita): self
    {
        if ($this->citas->removeElement($cita)) {
            
            if ($cita->getMedico() === $this) {
                $cita->setMedico(null);
            }
        }

        return $this;
    }
}

