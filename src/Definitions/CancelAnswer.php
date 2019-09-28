<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Definitions;

/**
 * Define the answer to the cancellation request (accept/reject)
 *
 * @method static self accept()
 * @method static self reject()
 * @method bool isAccept()
 * @method bool isReject()
 */
class CancelAnswer extends \PhpCfdi\XmlCancelacion\Definitions\CancelAnswer
{
}
