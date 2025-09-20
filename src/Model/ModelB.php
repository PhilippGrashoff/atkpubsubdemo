<?php declare(strict_types=1);

namespace AtkPubSubDemo\Model;

use Atk4\Data\Model;

class ModelB extends Model
{
    public $table = 'model_b';

    protected function init(): void
    {
        parent::init();
        $this->addField('name');
        $this->hasOne('model_a_id', ['model' => [ModelA::class]]);
    }
}