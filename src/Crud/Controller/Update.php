<?php

namespace Biig\Melodiia\Crud\Controller;

use Biig\Melodiia\Crud\Event\CrudEvent;
use Biig\Melodiia\Crud\Event\CustomResponseEvent;
use Biig\Melodiia\Crud\Persistence\DataStoreInterface;
use Biig\Melodiia\Crud\Tools\IdResolverInterface;
use Biig\Melodiia\Crud\Tools\SimpleIdResolver;
use Biig\Melodiia\Exception\IdMissingException;
use Biig\Melodiia\Exception\MelodiiaLogicException;
use Biig\Melodiia\Response\ApiResponse;
use Biig\Melodiia\Response\OkContent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Crud controller that update data model with the data from the request using a form.
 */
final class Update extends BaseCrudController
{
    use CrudControllerTrait;

    public const EVENT_PRE_UPDATE = 'melodiia.crud.pre_update';
    public const EVENT_POST_UPDATE = 'melodiia.crud.post_update';

    /** @var DataStoreInterface */
    private $dataStore;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var AuthorizationCheckerInterface */
    private $checker;

    /** @var IdResolverInterface */
    private $idResolver;

    public function __construct(DataStoreInterface $dataStore, FormFactoryInterface $formFactory, EventDispatcherInterface $dispatcher, AuthorizationCheckerInterface $checker, IdResolverInterface $idResolver = null)
    {
        parent::__construct($dispatcher);
        $this->dataStore = $dataStore;
        $this->formFactory = $formFactory;
        $this->checker = $checker;
        $this->idResolver = $idResolver ?? new SimpleIdResolver();
    }

    public function __invoke(Request $request): ApiResponse
    {
        $modelClass = $request->attributes->get(self::MODEL_ATTRIBUTE);
        $form = $request->attributes->get(self::FORM_ATTRIBUTE);
        $securityCheck = $request->attributes->get(self::SECURITY_CHECK, null);
        $clearMissing = $request->attributes->getBoolean(self::FORM_CLEAR_MISSING, null);

        try {
            $id = $this->idResolver->resolveId($request, $modelClass);
        } catch (IdMissingException $e) {
            throw new NotFoundHttpException('No id found', $e);
        }

        $this->assertModelClassInvalid($modelClass);

        $data = $this->dataStore->find($modelClass, $id);

        if ($securityCheck && !$this->checker->isGranted($securityCheck, $data)) {
            throw new AccessDeniedException(\sprintf('Access denied to data of type "%s".', $modelClass));
        }

        if (empty($form) || !class_exists($form)) {
            throw new MelodiiaLogicException('If you use melodiia CRUD classes, you need to specify a model.');
        }

        $formOrResponse = $this->decodeInputData($this->formFactory, $form, $request, $clearMissing, $data);
        if ($formOrResponse instanceof ApiResponse) {
            return $formOrResponse;
        }
        $form = $formOrResponse;

        $data = $form->getData();
        $this->dispatch(new CrudEvent($data), self::EVENT_PRE_UPDATE);

        $this->dataStore->save($data);
        $this->dispatch($event = new CustomResponseEvent($data), self::EVENT_POST_UPDATE);

        if ($event->hasCustomResponse()) {
            return $event->getResponse();
        }

        return new OkContent(['id' => $data->getId()]);
    }
}
