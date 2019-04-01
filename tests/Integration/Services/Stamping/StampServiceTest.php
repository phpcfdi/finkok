<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\StampedService;
use PhpCfdi\Finkok\Services\Stamping\StampingCommand;
use PhpCfdi\Finkok\Services\Stamping\StampingResult;
use PhpCfdi\Finkok\Services\Stamping\StampService;
use PhpCfdi\Finkok\Tests\Factories\RandomPreCfdi;
use PhpCfdi\Finkok\Tests\TestCase;

class StampServiceTest extends TestCase
{
    public function testStampPrecfdiWithErrorInDate(): void
    {
        $precfdi = (new RandomPreCfdi())->createInvalidByDate();
        $command = new StampingCommand($precfdi);

        $settings = $this->createSettingsFromEnvironment();
        $service = new StampService($settings);
        $result = $service->stamp($command);

        $this->assertGreaterThan(0, $result->alerts()->count());
        $this->assertSame('Fecha y hora de generación fuera de rango', $result->alerts()->first()->message());
    }

    public function testStampValidPrecfdi(): array
    {
        $precfdi = (new RandomPreCfdi())->createValid();
        $command = new StampingCommand($precfdi);

        $settings = $this->createSettingsFromEnvironment();
        $stampService = new StampService($settings);
        $stampResult = $stampService->stamp($command);

        $this->assertSame('Comprobante timbrado satisfactoriamente', $stampResult->statusCode());
        $this->assertCount(0, $stampResult->alerts());
        $this->assertNotEmpty($stampResult->xml());
        $this->assertNotEmpty($stampResult->uuid());
        $this->assertStringContainsString($stampResult->uuid(), $stampResult->xml());

        return ['precfdi' => $precfdi, 'stampResult' => $stampResult];
    }

    public function testStampValidPrecfdiTwoConsecutiveTimes(): void
    {
        $precfdi = (new RandomPreCfdi())->createValid();
        $command = new StampingCommand($precfdi);

        $settings = $this->createSettingsFromEnvironment();
        $service = new StampService($settings);

        $firstResult = $service->stamp($command);
        $this->assertSame('Comprobante timbrado satisfactoriamente', $firstResult->statusCode());

        $secondResult = $service->stamp($command);
        $this->assertSame(
            '307',
            $secondResult->alerts()->first()->errorCode(),
            'Finkok must alert that it was previously stamped'
        );

        if ('' === $secondResult->uuid()) {
            $this->markTestSkipped('Finkok no está devolviendo la información esperada, Ticket #17287');
        }

        $this->assertSame(
            $firstResult->uuid(),
            $secondResult->uuid(),
            'Finkok does not return the same UUID for duplicated stamp'
        );
    }

    /**
     * @param array $previous
     * @depends testStampValidPrecfdi
     * @return array
     */
    public function testStampedWithRecentlyCreatedCfdi(array $previous): array
    {
        /** @var string $precfdi */
        /** @var StampingResult $stampResult */
        ['precfdi' => $precfdi, 'stampResult' => $stampResult] = $previous;
        $command = new StampingCommand($precfdi);

        $settings = $this->createSettingsFromEnvironment();
        $stampedService = new StampedService($settings);

        // try this for 10 seconds max
        $runUntilTime = time() + 10;
        do {
            $stampedResult = $stampedService->stamped($command);
            if ('' === $stampedResult->uuid() && $runUntilTime <= time()) {
                sleep(1);
                continue;
            }
        } while (false);

        $this->assertSame($stampResult->uuid(), $stampResult->uuid(), 'No se pudo recuperar el CFDI recién creado');

        return ['precfdi' => $precfdi, 'stampResult' => $stampResult];
    }

    /**
     * @param array $previous
     * @depends testStampedWithRecentlyCreatedCfdi
     */
    public function testStampTwoConsecutiveTimesCheckingStampedFirst(array $previous): void
    {
        /** @var string $precfdi */
        /** @var StampingResult $stampResult */
        ['precfdi' => $precfdi, 'stampResult' => $stampResult] = $previous;
        $command = new StampingCommand($precfdi);

        $settings = $this->createSettingsFromEnvironment();
        $service = new StampService($settings);

        $secondResult = $service->stamp($command);
        $this->assertSame(
            '307',
            $secondResult->alerts()->first()->errorCode(),
            'Finkok must alert that it was previously stamped'
        );
        $this->assertSame(
            $stampResult->uuid(),
            $secondResult->uuid(),
            'Finkok does not return the same UUID for duplicated stamp'
        );
    }
}
