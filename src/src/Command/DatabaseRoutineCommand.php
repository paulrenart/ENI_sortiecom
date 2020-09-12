<?php

namespace App\Command;

use App\Entity\Sorties;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DatabaseRoutineCommand extends Command
{
    protected static $defaultName = 'databaseRoutine';
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Update sorties status in database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sortie_repository = $this->em->getRepository(Sorties::class);
        $sorties = $sortie_repository->findAll();
        
        $now = new \DateTime('now');

        $i = 0;
        foreach ($sorties as $sortie) {
            if ($sortie->getEtat() == 1 && $sortie->getDateFin() < $now)
            {
                $sortie->setEtat(2);
                $this->em->persist($sortie);
                $this->em->flush();
                $i++;
            }
        }

        $io = new SymfonyStyle($input, $output);
        $io->success($i." sorties edited");

        return Command::SUCCESS;
    }
}
