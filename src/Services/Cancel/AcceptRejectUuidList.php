<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use ArrayIterator;
use OutOfRangeException;
use PhpCfdi\Finkok\Definitions\CancelAnswer;
use PhpCfdi\Finkok\Services\AbstractCollection;
use stdClass;

/**
 * @method AcceptRejectUuidItem get(int $index)
 * @method AcceptRejectUuidItem first()
 * @method ArrayIterator|AcceptRejectUuidItem[] getIterator()
 * @extends AbstractCollection<AcceptRejectUuidItem>
 */
class AcceptRejectUuidList extends AbstractCollection
{
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
            $source = $content->{'Acepta'};
            $answer = CancelAnswer::accept();
        } elseif (isset($content->{'Rechaza'})) {
            $source = $content->{'Rechaza'};
            $answer = CancelAnswer::reject();
        } else {
            $source = (object)[];
            $answer = CancelAnswer::accept();
        }
        return new AcceptRejectUuidItem(
            strval($source->uuid ?? ''),
            new AcceptRejectUuidStatus($source->status ?? '0'),
            $answer
        );
    }
}
