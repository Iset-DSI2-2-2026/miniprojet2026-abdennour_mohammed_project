<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class MaListeService
{
    private const SESSION_KEY = 'bookshelf_ma_liste_ids';

    public function __construct(private RequestStack $requestStack)
    {
    }

    /**
     * @return list<int>
     */
    public function getIds(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request?->hasSession()) {
            return [];
        }

        $session = $request->getSession();
        /** @var list<int>|null $ids */
        $ids = $session->get(self::SESSION_KEY);

        return \is_array($ids) ? array_values(array_unique(array_map('intval', $ids))) : [];
    }

    public function add(int $livreId): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request?->hasSession()) {
            return;
        }

        $ids = $this->getIds();
        if (!\in_array($livreId, $ids, true)) {
            $ids[] = $livreId;
        }
        $request->getSession()->set(self::SESSION_KEY, $ids);
    }

    public function remove(int $livreId): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request?->hasSession()) {
            return;
        }

        $ids = array_values(array_filter($this->getIds(), static fn (int $id): bool => $id !== $livreId));
        $request->getSession()->set(self::SESSION_KEY, $ids);
    }

    public function count(): int
    {
        return \count($this->getIds());
    }
}
