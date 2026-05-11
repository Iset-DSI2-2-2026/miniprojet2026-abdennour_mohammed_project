<?php

namespace App\Security\Voter;

use App\Entity\Livre;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class LivreVoter extends Voter
{
    public const EDIT = 'LIVRE_EDIT';

    public const DELETE = 'LIVRE_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::EDIT, self::DELETE], true) && $subject instanceof Livre;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        if (\in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        $livre = $subject;
        \assert($livre instanceof Livre);

        $owner = $livre->getAjoutePar();

        return $owner && $owner->getId() === $user->getId();
    }
}
