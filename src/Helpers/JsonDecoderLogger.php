<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Helpers;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Stringable;

/**
 * Esta clase es un adaptador para convertir un mensaje de registro (log) que está
 * en formato Json y es decodificado y convertido en texto a través de la función
 * print_r, luego pasa el mensaje al logger con el que fue construido el objeto.
 *
 * Si el mensaje no es un Json no válido entonces pasa sin convertirse.
 *
 * Tiene algunas opciones:
 * - alsoLogJsonMessage: Envía los dos mensajes, tanto el texto como el json al logger.
 * - useJsonValidateIfAvailable: Usa \json_validate() si está disponible.
 */
final class JsonDecoderLogger extends AbstractLogger implements LoggerInterface
{
    private bool $useJsonValidateIfAvailable = true;

    private bool $alsoLogJsonMessage = false;

    private bool $lastMessageWasJsonValid = false;

    public function __construct(private LoggerInterface $logger)
    {
    }

    /**
     * Define si se utilizará la función \json_validate en caso de estar disponible.
     *
     * @param bool|null $value El nuevo estado, si se establece NULL entonces solo devuelve el estado previo.
     * @return bool El estado previo
     */
    public function setUseJsonValidateIfAvailable(?bool $value = null): bool
    {
        $previous = $this->useJsonValidateIfAvailable;
        if (null !== $value) {
            $this->useJsonValidateIfAvailable = $value;
        }
        return $previous;
    }

    /**
     * Define si también se mandará el mensaje JSON al Logger.
     *
     * @param bool|null $value El nuevo estado, si se establece NULL entonces solo devuelve el estado previo.
     * @return bool El estado previo
     */
    public function setAlsoLogJsonMessage(?bool $value = null): bool
    {
        $previous = $this->alsoLogJsonMessage;
        if (null !== $value) {
            $this->alsoLogJsonMessage = $value;
        }
        return $previous;
    }

    public function lastMessageWasJsonValid(): bool
    {
        return $this->lastMessageWasJsonValid;
    }

    /**
     * @inheritDoc
     * @param mixed[] $context
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->logger->log($level, $this->jsonDecode($message), $context);
        if ($this->lastMessageWasJsonValid && $this->alsoLogJsonMessage) {
            $this->logger->log($level, $message, $context);
        }
    }

    private function jsonDecode(string|Stringable $string): string
    {
        $this->lastMessageWasJsonValid = false;

        // json_validate and json_decode
        if ($this->useJsonValidateIfAvailable) {
            $string = $this->jsonDecodeWithValidate($string);
            if ($this->lastMessageWasJsonValid()) {
                return $string;
            }
        }

        // json_decode only
        $string = (string) $string;
        $decoded = json_decode($string);
        if (JSON_ERROR_NONE === json_last_error()) {
            $this->lastMessageWasJsonValid = true;
            return $this->varDump($decoded);
        }

        return $string;
    }

    private function jsonDecodeWithValidate(string|Stringable $string): string
    {
        $string = (string) $string;
        if (function_exists('json_validate') && json_validate($string)) {
            $this->lastMessageWasJsonValid = true;
            return $this->varDump(json_decode($string));
        }

        return $string;
    }

    private function varDump(mixed $var): string
    {
        return print_r($var, true);
    }
}
