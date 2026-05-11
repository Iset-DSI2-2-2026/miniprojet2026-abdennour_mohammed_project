<?php

namespace App\Twig;

use App\Service\MaListeService;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFilter;
use Twig\TwigFunction;

class BookShelfExtension extends AbstractExtension
{
    public function __construct(private MaListeService $maListeService)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('time_ago', $this->timeAgo(...)),
            new TwigFilter('reading_time', $this->readingTime(...), ['is_safe' => ['html']]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('book_status_badge', $this->bookStatusBadge(...), ['is_safe' => ['html']]),
            new TwigFunction('ma_liste_count', fn (): int => $this->maListeService->count()),
        ];
    }

    public function timeAgo(\DateTimeInterface $date): string
    {
        $now = new \DateTimeImmutable();
        $target = \DateTimeImmutable::createFromInterface($date);
        if ($target > $now) {
            return 'bientôt';
        }

        $diff = $now->diff($target);
        $totalDays = (int) $diff->format('%a');
        if ($diff->y > 0) {
            return 'il y a '.$diff->y.' an'.($diff->y > 1 ? 's' : '');
        }
        if ($diff->m > 0) {
            return 'il y a '.$diff->m.' mois';
        }
        if ($totalDays >= 7) {
            $weeks = intdiv($totalDays, 7);

            return 'il y a '.$weeks.' semaine'.($weeks > 1 ? 's' : '');
        }
        if ($totalDays > 0) {
            return 'il y a '.$totalDays.' jour'.($totalDays > 1 ? 's' : '');
        }
        if ($diff->h > 0) {
            return 'il y a '.$diff->h.' heure'.($diff->h > 1 ? 's' : '');
        }
        if ($diff->i > 0) {
            return 'il y a '.$diff->i.' minute'.($diff->i > 1 ? 's' : '');
        }

        return "à l'instant";
    }

    public function readingTime(int $nbPages): string
    {
        $totalMinutes = (int) round($nbPages / 30 * 60);
        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;
        if ($hours > 0 && $minutes > 0) {
            return $hours.'h'.str_pad((string) $minutes, 2, '0', STR_PAD_LEFT).' de lecture';
        }
        if ($hours > 0) {
            return $hours.'h de lecture';
        }
        if ($minutes > 0) {
            return $minutes.' min de lecture';
        }

        return 'moins d\'une minute de lecture';
    }

    public function bookStatusBadge(bool $disponible): Markup
    {
        if ($disponible) {
            return new Markup('<span class="badge text-bg-success">🟢 Disponible</span>', 'UTF-8');
        }

        return new Markup('<span class="badge text-bg-danger">🔴 Indisponible</span>', 'UTF-8');
    }
}
