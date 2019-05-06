<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok;

class FinkokEnvironment
{
    /** @var Definitions\Environment */
    private $environment;

    private function __construct(Definitions\Environment $environment)
    {
        $this->environment = $environment;
    }

    /** @return static */
    public static function makeDevelopment(): self
    {
        return new self(Definitions\Environment::development());
    }

    /** @return static */
    public static function makeProduction(): self
    {
        return new self(Definitions\Environment::production());
    }

    public function isDevelopment(): bool
    {
        return $this->environment->isDevelopment();
    }

    public function isProduction(): bool
    {
        return $this->environment->isProduction();
    }

    public function server(): string
    {
        return $this->environment->value();
    }

    public function endpoint(Definitions\Services $service): string
    {
        $environment = $this->environment;
        if ($service->isManifest()) {
            $environment = (new Definitions\EnvironmentManifest($environment->index()));
        }

        return $environment->value() . $service->value();
    }
}
