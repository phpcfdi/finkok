<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

class ObtainCustomersCommand
{
    public function __construct(private int $page)
    {
    }

    public function page(): int
    {
        return $this->page;
    }
}
