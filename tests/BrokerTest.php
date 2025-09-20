<?php declare(strict_types=1);

namespace AtkPubSubDemo\Tests;

use Atk4\Data\Persistence\Sql;
use Atk4\Data\Schema\TestCase;
use AtkPubSubDemo\Model\ModelA;
use AtkPubSubDemo\Model\ModelB;
use AtkPubSubDemo\SomeOtherRepo\SampleController;
use AtkPubSubDemo\YetAnotherRepo\YetAnotherController;

class BrokerTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = Sql::connect('sqlite::memory:');
        $this->createMigrator(new ModelA($this->db))->dropIfExists()->create();
        $this->createMigrator(new ModelB($this->db))->dropIfExists()->create();
    }

    public function testBrokering(): void
    {
        //register subscriptions. This would typically happen in Aoo::construct()
        SampleController::registerSubscriptions();
        YetAnotherController::registerSubscriptions();

        //this should not add any ModelB entities
        $modelAEntity = (new ModelA($this->db))->createEntity();
        $modelAEntity->save();

        //this should create a new ModelB entity with the name = Tina, meaning SampleController performs an additional action
        $modelAEntity->set('some_field', 1);
        $modelAEntity->save();

        $modelB = (new ModelB($this->db));
        self::assertSame(1, (int)$modelB->action('count')->getOne());
        $modelBEntity = $modelB->loadAny();
        self::assertSame('Tina', $modelBEntity->get('name'));

        //this should create a new ModelB entity with the name = Hans, meaning YetAnotherController performs an additional action
        $modelAEntity->set('some_field', 2);
        $modelAEntity->save();

        $modelB = (new ModelB($this->db))->setOrder('id', 'DESC');
        self::assertSame(2, (int)$modelB->action('count')->getOne());
        $modelBEntity = $modelB->loadAny();
        self::assertSame('Hans', $modelBEntity->get('name'));
    }
}