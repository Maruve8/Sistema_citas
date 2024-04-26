<?php

namespace App\Entity;

use App\Repository\CitaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CitaRepository::class)]
class Cita
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaHora = null;

    #[ORM\Column(length: 255)]
    private ?string $estado = null;

    #[ORM\ManyToOne(inversedBy: 'citas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $paciente = null;

    #[ORM\ManyToOne(inversedBy: 'citas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Medico $médico = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaHora(): ?\DateTimeInterface
    {
        return $this->fechaHora;
    }

    public function setFechaHora(\DateTimeInterface $fechaHora): static
    {
        $this->fechaHora = $fechaHora;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getPaciente(): ?usuario
    {
        return $this->paciente;
    }

    public function setPaciente(?Usuario $paciente): static
    {
        $this->paciente = $paciente;

        return $this;
    }

    public function getMédico(): ?medico
    {
        return $this->médico;
    }

    public function setMédico(?Medico $médico): static
    {
        $this->médico = $médico;

        return $this;
    }
}
