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

    //    /**
    //     * @return Cita[] Returns an array of Cita objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Cita
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


    public function findAvailableTimesByDate(\DateTime $fecha, $medicoId, $especialidadId)
{
    $qb = $this->createQueryBuilder('c')
        ->select('c.fechaHora')
        ->where('c.fechaHora BETWEEN :startOfDay AND :endOfDay')
        ->andWhere('c.medico = :medicoId')
        ->andWhere('c.especialidad = :especialidadId')
        ->setParameter('startOfDay', $fecha->setTime(0, 0))
        ->setParameter('endOfDay', $fecha->setTime(23, 59, 59))
        ->setParameter('medicoId', $medicoId)
        ->setParameter('especialidadId', $especialidadId);

    $takenTimes = $qb->getQuery()->getResult();

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

    //comienzan los cálculos de horas disponibles:

    $availableTimes = [];
    foreach ($period as $dt) {
        $availableTimes[] = $dt->format('H:i');
    }

    // Eliminar horas ocupadas
    $takenHours = array_map(function ($entry) {
        return $entry['fechaHora']->format('H:i');
    }, $takenTimes);

    $availableTimes = array_diff($availableTimes, $takenHours);

    return $availableTimes;
}


}
