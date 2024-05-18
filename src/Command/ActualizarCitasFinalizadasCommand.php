<?php
namespace App\Command;

use App\Repository\CitaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ActualizarCitasFinalizadasCommand extends Command
{
    protected static $defaultName = 'app:actualizar-citas-finalizadas';

    private $citaRepository;
    private $entityManager;

    public function __construct(CitaRepository $citaRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->citaRepository = $citaRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
        ->setName('app:actualizar-citas-finalizadas')
        ->setDescription('Actualiza el estado de las citas a "Finalizada" si ya han tenido lugar.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $citas = $this->citaRepository->findBy(['estado' => 'Confirmada']);
        $now = new \DateTime();

        foreach ($citas as $cita) {
            if ($cita->getFechaHora() < $now) {
                $cita->setEstado('Finalizada');
            }
        }

        $this->entityManager->flush();

        $io->success('Las citas han sido actualizadas.');

        return Command::SUCCESS;
    }
}