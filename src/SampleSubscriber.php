<?php declare(strict_types=1);

namespace AtkPubSubDemo;

use Atk4\Data\Model;
use Model\ModelA;
use SomeOtherRepo\SampleController;
use YetAnotherRepo\YetAnotherController;

class SampleSubscriber
{

    public static function registerSubscriptions(): void
    {
        Broker::get()->subscribe(
            Model::HOOK_AFTER_SAVE,
            function (Model $entity, bool $isUpdate) {
                SampleController::handleModelSave($entity, $isUpdate);
                YetAnotherController::handleModelSave($entity, $isUpdate);;
            }
        );
    }
}