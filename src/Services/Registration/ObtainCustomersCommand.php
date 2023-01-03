<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

class ObtainCustomersCommand
{
    /** @var int */
    private $page;

    public function __construct(int $page)
    {
        $this->page = $page;
    }

    public function page(): int
    {
        return $this->page;
    }
}
