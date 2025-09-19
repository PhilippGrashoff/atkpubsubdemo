<?php declare(strict_types=1);

namespace YetAnotherRepo;

use Atk4\Data\Model;
use Model\ModelA;
use Model\ModelB;

class YetAnotherController
{
    public static function handleModelSave(Model $model, bool $isUpdate): void
    {
        match (get_class($model)) {
            ModelA::class => self::handleModelASave($model, $isUpdate),
            default => null
        };
    }

    protected static function handleModelASave(ModelA $modelA, bool $isUpdate): void {

        if ($modelA->get('some_field') === 2) {
            (new ModelB($modelA->getPersistence()))->createEntity()
                ->set('model_a_id', $modelA->getId())
                ->set('name', 'Hans')
                ->save();
        }
    }
}