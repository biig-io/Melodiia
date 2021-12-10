<?php

declare(strict_types=1);

namespace TestApplication\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Nekland\Tools\StringTools;
use SwagIndustries\Melodiia\Response\Model\Collection;
use SwagIndustries\Melodiia\Response\OkContent;
use TestApplication\Entity\Todo;

class SimulateExceptionAction
{
    /**
     * Simulate an exception while processing a controller
     */
    public function __invoke(EntityManagerInterface $manager, $word)
    {
        throw new \Exception('oupsii, it\'s broken ! :D');
    }
}
