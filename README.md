# atkpubsubdemo

A small demo showing how Atk4\Core hooks can be created to create some pub/sub style action handling. The motivation
is to get logic what should happen after `Model::save()` or `Model::delete()` away from the Model hooks directly.

There is a small `Broker` implemented as singleton. The Model just `publish()` their events (e.g. `Model::HOOK_AFTER_SAVE`) 
to this broker.
Other code can then `subscribe()` these events and act accordingly. In this sample repo, the 2 Controllers `SampleController` 
and `YetAnotherController` reside within the same repository, but they could be in different repositories which just
require the base repository the Broker (and typically the Models) reside in.

See or run the test code for a simple example. It illustrates that `ModelA` is unaware of any additional logic which
should happen when it is saved but just publishes its after save event to the Broker. The 2 Controllers subscribe the 
after save event and act accordingly.

The main benefits are:
- No control flow logic within the Model hooks. The scope of the model can focus on the model itself and does not need to know the complete logic of your application.
- This way, it is easier to split up an application into several repositories. As `ModelA` no longer needs to know what should happen when it is saved, all of this logic can easily be moved elsewhere. 

Without this, `ModelA::init()` would typically have looked like this, containing all the additional logic (or direct calls to it): 
```php

    protected function init(): void
    {
        parent::init();
        $this->addField('some_field', ['type' => 'integer']);
        $this->hasMany(ModelB::class, ['model' => [ModelB::class, 'theirField' => 'model_a_id']]);

        $this->onHook(
            Model::HOOK_AFTER_SAVE,
            function (self $entity, bool $isUpdate) {
                if ($entity->get('some_field') === 1) {
                    (new ModelB($entity->getModel()->getPersistence()))->createEntity()
                        ->set('model_a_id', $entity->getId())
                        ->set('name', 'Tina')
                        ->save();
                }
                elseif($entity->get('some_field') === 2) {
                    (new ModelB($entity->getModel()->getPersistence()))->createEntity()
                        ->set('model_a_id', $entity->getId())
                        ->set('name', 'Hans')
                        ->save();
                }
            }
        );

        $this->onHook(
            Model::HOOK_AFTER_DELETE,
            function (self $entity) {
                Broker::get()->publish(Model::HOOK_AFTER_DELETE, [$entity]);
            }
        );
    }
```