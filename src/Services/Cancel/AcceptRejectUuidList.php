<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use OutOfRangeException;
use PhpCfdi\Finkok\Definitions\CancelAnswer;
use PhpCfdi\Finkok\Internal\MethodsFilterVariablesTrait;
use PhpCfdi\Finkok\Services\AbstractCollection;
use stdClass;

/**
 * @extends AbstractCollection<AcceptRejectUuidItem>
 */
class AcceptRejectUuidList extends AbstractCollection
{
    use MethodsFilterVariablesTrait;

    public function findByUuidOrFail(string $uuid): AcceptRejectUuidItem
    {
        $found = $this->findByUuid($uuid);
        if (null === $found) {
            throw new OutOfRangeException(sprintf('UUID %s not found on result', $uuid));
        }
        return $found;
    }

    public function findByUuid(string $uuid): ?AcceptRejectUuidItem
    {
        foreach ($this->getIterator() as $item) {
            if (0 === strcasecmp($item->uuid(), $uuid)) {
                return $item;
            }
        }
        return null;
    }

    protected function createItemFromStdClass(stdClass $content): object
    {
        if (isset($content->{'Acepta'})) {
            /** @var stdClass $source */
            $source = $content->{'Acepta'};
            $answer = CancelAnswer::accept();
        } elseif (isset($content->{'Rechaza'})) {
            /** @var stdClass $source */
            $source = $content->{'Rechaza'};
            $answer = CancelAnswer::reject();
        } else {
            $source = (object)[];
            $answer = CancelAnswer::accept();
        }
        return new AcceptRejectUuidItem(
            $this->filterString($source->uuid ?? ''),
            new AcceptRejectUuidStatus($this->filterString($source->status ?? '0')),
            $answer
        );
    }
}
