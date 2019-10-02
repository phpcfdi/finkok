<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use Eclipxe\MicroCatalog\MicroCatalog;

final class AcceptRejectUuidStatus extends MicroCatalog
{
    public static function getEntriesArray(): array
    {
        return [
            '1000' => 'Se recibió la respuesta de la petición de forma exitosa',
            '1001' => 'No existen peticiones de cancelación en espera de respuesta para el UUID',
            '1002' => 'Ya se recibió una respuesta para la petición de cancelación del UUID',
            '1003' => 'El sello no corresponde al RFC receptor',
            '1004' => 'Existen más de una petición de cancelación para el mismo UUID',
            '1005' => 'El UUID es nulo o no posee el formato correcto',
            '1006' => 'Se rebasó el número máximo de solicitudes permitidas',
        ];
    }

    public function getEntryValueOnUndefined()
    {
        return 'Respuesta del SAT desconocida';
    }

    public function getCode(): string
    {
        return $this->getEntryId();
    }

    public function getMessage(): string
    {
        return strval($this->getEntryValue());
    }

    public function isSuccess(): bool
    {
        return ('1000' === $this->getCode());
    }
}
