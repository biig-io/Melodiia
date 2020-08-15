<?php

declare(strict_types=1);

namespace SwagIndustries\Melodiia\Crud\Controller;

use SwagIndustries\Melodiia\Crud\CrudableModelInterface;
use SwagIndustries\Melodiia\Exception\MelodiiaLogicException;

trait CrudControllerTrait
{
    /**
     * @param string $modelClass FQCN of the model class
     *
     * @throws MelodiiaLogicException if the model class don't match Melodiia requirements
     */
    public function assertModelClassInvalid(string $modelClass): void
    {
        if (empty($modelClass) || !class_exists($modelClass) || !is_subclass_of($modelClass, CrudableModelInterface::class)) {
            throw new MelodiiaLogicException('If you use melodiia CRUD classes, you need to specify a model.');
        }
    }
}
