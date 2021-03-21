<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\GetSatStatusResult;
use PhpCfdi\Finkok\Tests\TestCase;

final class GetSatStatusResultTest extends TestCase
{
    public function testResultUsingPredefinedResponse(): void
    {
        $data = json_decode($this->fileContentPath('cancel-get-sat-status-response.json'));
        $result = new GetSatStatusResult($data);
        $this->assertSame('S - Comprobante obtenido satisfactoriamente.', $result->query());
        $this->assertSame('Vigente', $result->cfdi());
        $this->assertSame('Cancelable sin aceptación', $result->cancellable());
        $this->assertSame('En proceso', $result->cancellation());
    }
}
