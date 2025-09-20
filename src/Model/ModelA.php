<?php declare(strict_types=1);

namespace AtkPubSubDemo\Model;

use Atk4\Data\Model;
use AtkPubSubDemo\Broker;

class ModelA extends Model
{
    public $table = 'model_a';

    protected function init(): void
    {
        parent::init();
        $this->addField('some_field', ['type' => 'integer']);
        $this->hasMany(ModelB::class, ['model' => [ModelB::class, 'theirField' => 'model_a_id']]);

        $this->onHook(
            Model::HOOK_AFTER_SAVE,
            function (self $entity, bool $isUpdate) {
                Broker::get()->publish(Model::HOOK_AFTER_SAVE, [$entity, $isUpdate]);
            }
        );

        $this->onHook(
            Model::HOOK_AFTER_DELETE,
            function (self $entity) {
                Broker::get()->publish(Model::HOOK_AFTER_DELETE, [$entity]);
            }
        );
    }
}