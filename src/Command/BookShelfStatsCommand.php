<?php

namespace App\Command;

use App\Repository\AuteurRepository;
use App\Repository\GenreRepository;
use App\Repository\LivreRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:bookshelf:stats',
    description: 'Affiche les statistiques de la bibliothèque',
)]
class BookShelfStatsCommand extends Command
{
    public function __construct(
        private LivreRepository $livreRepository,
        private AuteurRepository $auteurRepository,
        private GenreRepository $genreRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('detail', null, InputOption::VALUE_NONE, 'Affiche le détail par genre')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Format de sortie (table, json)', 'table');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $format = (string) $input->getOption('format');

        $total = (int) $this->livreRepository->count([]);
        $dispo = (int) $this->livreRepository->count(['disponible' => true]);
        $indispo = $total - $dispo;
        $auteurs = (int) $this->auteurRepository->count([]);
        $genres = (int) $this->genreRepository->count([]);

        $pages = (int) $this->livreRepository->createQueryBuilder('l')
            ->select('COALESCE(SUM(l.nbPages), 0)')
            ->getQuery()
            ->getSingleScalarResult();
        $tempsLecture = $pages / 30.0;

        $qb = $this->livreRepository->createQueryBuilder('l')
            ->select('g.nom AS nom', 'COUNT(l.id) AS cnt')
            ->join('l.genre', 'g')
            ->groupBy('g.id')
            ->orderBy('cnt', 'DESC')
            ->setMaxResults(3);

        $topGenres = $qb->getQuery()->getArrayResult();

        if ('json' === $format) {
            $io->writeln(json_encode([
                'total_livres' => $total,
                'disponibles' => $dispo,
                'indisponibles' => $indispo,
                'auteurs' => $auteurs,
                'genres' => $genres,
                'temps_lecture_heures' => round($tempsLecture, 2),
                'top_genres' => $topGenres,
            ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

            return Command::SUCCESS;
        }

        $io->title('Statistiques BookShelf');
        $io->table(
            ['Indicateur', 'Valeur'],
            [
                ['Livres (total)', (string) $total],
                ['Livres disponibles', (string) $dispo],
                ['Livres indisponibles', (string) $indispo],
                ['Auteurs', (string) $auteurs],
                ['Genres', (string) $genres],
                ['Temps de lecture estimé (h)', number_format($tempsLecture, 2, ',', ' ')],
            ],
        );

        $io->section('Top 3 des genres les plus représentés');
        $rows = [];
        foreach ($topGenres as $row) {
            $rows[] = [(string) $row['nom'], (string) $row['cnt']];
        }
        $io->table(['Genre', 'Livres'], $rows);

        if ($input->getOption('detail')) {
            $io->section('Détail par genre');
            $detail = $this->livreRepository->createQueryBuilder('l')
                ->select('g.nom AS nom', 'COUNT(l.id) AS cnt')
                ->join('l.genre', 'g')
                ->groupBy('g.id')
                ->orderBy('g.nom', 'ASC')
                ->getQuery()
                ->getArrayResult();
            $detailRows = [];
            foreach ($detail as $row) {
                $detailRows[] = [(string) $row['nom'], (string) $row['cnt']];
            }
            $io->table(['Genre', 'Livres'], $detailRows);
        }

        $io->success('Terminé.');

        return Command::SUCCESS;
    }
}
