<?php

namespace App\Repository;

use App\Entity\Cita;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use \App\Entity\Medico;

/**
 * @extends ServiceEntityRepository<Cita>
 *
 * @method Cita|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cita|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cita[]    findAll()
 * @method Cita[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CitaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cita::class);
    }

    
    public function findAvailableTimesByDate(\DateTime $fecha, $medicoId, $especialidadId)
    {
        $startOfDay = clone $fecha;
        $startOfDay->setTime(0, 0, 0);
        $endOfDay = clone $fecha;
        $endOfDay->setTime(23, 59, 59);

        $estadoConfirmada = 'Confirmada';

        // Log de depuración para confirmar los valores de los parámetros
        error_log('Fecha en repositorio: ' . $fecha->format('Y-m-d'));
        error_log('Medico ID en repositorio: ' . $medicoId);
        error_log('Especialidad ID en repositorio: ' . $especialidadId);
        error_log('Estado Confirmada en repositorio: ' . $estadoConfirmada);

        $qb = $this->createQueryBuilder('c')
            ->select('c.fechaHora')
            ->where('c.fechaHora BETWEEN :startOfDay AND :endOfDay')
            ->andWhere('c.medico = :medicoId')
            ->andWhere('c.especialidad = :especialidadId')
            ->andWhere('c.estado != :estadoConfirmada')
            ->setParameter('startOfDay', $startOfDay)
            ->setParameter('endOfDay', $endOfDay)
            ->setParameter('medicoId', $medicoId)
            ->setParameter('especialidadId', $especialidadId)
            ->setParameter('estadoConfirmada', $estadoConfirmada);

        $takenTimes = $qb->getQuery()->getResult();

        // Depuración
        error_log('Citas confirmadas encontradas en el repositorio: ' . print_r($takenTimes, true));

        return $takenTimes;
    }

    private function calculateAvailableTimes(\DateTime $fecha, array $takenTimes, $medicoId)
    {
        // Obtener el turno del médico
        $medico = $this->getEntityManager()->getRepository(Medico::class)->find($medicoId);
        $turno = $medico->getTurno(); 

        // Definir las horas de inicio y fin basadas en el turno
        $startTime = ($turno === 'manana') ? 8 : 15; // 8h para la mañana, 15h para la tarde
        $endTime = ($turno === 'manana') ? 14 : 20; // 15h para la mañana, 21h para la tarde (última cita comienza una hora antes del fin de turno)

        // Crear todas las posibles horas de inicio de citas dentro del turno
        $startDateTime = clone $fecha;
        $startDateTime->setTime($startTime, 0);
        $endDateTime = clone $fecha;
        $endDateTime->setTime($endTime, 45); // Última cita empieza a las 14:45 o 20:45

        $interval = new \DateInterval('PT15M'); // Intervalo de 15 minutos entre citas
        $period = new \DatePeriod($startDateTime, $interval, $endDateTime);

        // Obtener la hora actual para filtrar las horas pasadas
        $now = new \DateTime();
        if ($fecha->format('Y-m-d') === $now->format('Y-m-d')) {
            $now = $now->format('H:i:s');
        } else {
            $now = '00:00:00';
        }

        $availableTimes = [];
        foreach ($period as $dt) {
            if ($dt->format('H:i:s') > $now) {
                $availableTimes[] = $dt->format('H:i:s');
            }
        }

        // Eliminar horas ocupadas
        $takenHours = array_map(function ($entry) {
            return $entry['fechaHora']->format('H:i:s');
        }, $takenTimes);

        // Depuración: Verificar las horas ocupadas
        error_log('Horas Ocupadas: ' . print_r($takenHours, true));

        $availableTimes = array_diff($availableTimes, $takenHours);

        // Depuración: Verificar las horas disponibles después del filtrado
        error_log('Horas Disponibles después del Filtrado: ' . print_r($availableTimes, true));

        return $availableTimes;
    }
}