<?php

namespace App\Mail;

use App\Entity\Livre;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

final class LivreCreatedMailSender
{
    public function __construct(
        private MailerInterface $mailer,
        #[Autowire('%env(BOOKSHELF_NOTIFY_EMAIL)%')]
        private string $notifyTo,
    ) {
    }

    public function send(Livre $livre): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('noreply@bookshelf.com', 'BookShelf'))
            ->to($this->notifyTo)
            ->subject('📗 Nouveau livre ajouté : '.$livre->getTitre())
            ->htmlTemplate('emails/nouveau_livre.html.twig')
            ->context([
                'livre' => $livre,
                'utilisateur' => $livre->getAjoutePar(),
            ]);

        $this->mailer->send($email);
    }
}
