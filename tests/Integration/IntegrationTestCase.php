<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration;

use PhpCfdi\Finkok\Services\Stamping\QuickStampService;
use PhpCfdi\Finkok\Services\Stamping\StampingCommand;
use PhpCfdi\Finkok\Services\Stamping\StampingResult;
use PhpCfdi\Finkok\Tests\Factories\RandomPreCfdi;
use PhpCfdi\Finkok\Tests\TestCase;

class IntegrationTestCase extends TestCase
{
    /** @var array */
    protected static $statics = [];

    public function newStampingCommand(): StampingCommand
    {
        return new StampingCommand((new RandomPreCfdi())->createValid());
    }

    public function newStampingCommandInvalidDate(): StampingCommand
    {
        return new StampingCommand((new RandomPreCfdi())->createInvalidByDate());
    }

    public function currentStampingCommand(): StampingCommand
    {
        if (! isset(static::$statics['stampingCommand'])) {
            static::$statics['stampingCommand'] = $this->newStampingCommand();
        }
        return static::$statics['stampingCommand'];
    }

    public function newCfdi(StampingCommand $stampingCommand): StampingResult
    {
        $settings = $this->createSettingsFromEnvironment();
        $service = new QuickStampService($settings);
        return $service->quickstamp($stampingCommand);
    }

    public function currentCfdi(): StampingResult
    {
        if (! isset(static::$statics['cfdi'])) {
            static::$statics['cfdi'] = $this->newCfdi($this->currentStampingCommand());
        }
        return static::$statics['cfdi'];
    }
}
