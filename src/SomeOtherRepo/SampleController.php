<?php declare(strict_types=1);

namespace AtkPubSubDemo\SomeOtherRepo;

use Atk4\Data\Model;
use AtkPubSubDemo\Broker;
use AtkPubSubDemo\Model\ModelA;
use AtkPubSubDemo\Model\ModelB;

class SampleController
{

    public static function registerSubscriptions(): void
    {
        Broker::get()->subscribe(
            Model::HOOK_AFTER_SAVE,
            function (Model $entity, bool $isUpdate) {
                match (get_class($entity)) {
                    ModelA::class => self::handleModelASave($entity, $isUpdate),
                    default => null
                };
            }
        );
    }

    protected static function handleModelASave(ModelA $entity, bool $isUpdate): void
    {
        if ($entity->get('some_field') === 1) {
            (new ModelB($entity->getModel()->getPersistence()))->createEntity()
                ->set('model_a_id', $entity->getId())
                ->set('name', 'Tina')
                ->save();
        }
    }
}