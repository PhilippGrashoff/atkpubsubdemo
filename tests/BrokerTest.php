<?php declare(strict_types=1);

namespace AtkPubSubDemo\Tests;

use Atk4\Data\Persistence\Sql;
use Atk4\Data\Schema\TestCase;
use AtkPubSubDemo\SampleSubscriber;
use Model\ModelA;
use Model\ModelB;

class BrokerTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = Sql::connect('sqlite::memory:');
    }

    public function testBrokering(): void
    {
        //register subscriptions. This would typically happen in Aoo::construct()
        SampleSubscriber::registerSubscriptions();

        //this should not add any ModelB entities
        $modelAEntity = (new ModelA($this->db))->createEntity();
        $modelAEntity->save();

        //this should create a new ModelB entity with name = Tina
        $modelAEntity->set('some_field', 1);
        $modelAEntity->save();

        $modelB = (new ModelB($this->db));
        self::assertSame(1, (int)$modelB->action('count')->getOne());
        $modelBEntity = $modelB->loadAny();
        self::assertSame('Tina', $modelBEntity->get('name'));

        //this should create a new ModelB entity with name = Tina
        $modelAEntity->set('some_field', 2);
        $modelAEntity->save();

        $modelB = (new ModelB($this->db))->setOrder('id', 'DESC');
        self::assertSame(2, (int)$modelB->action('count')->getOne());
        $modelBEntity = $modelB->loadAny();
        self::assertSame('Hans', $modelBEntity->get('name'));
    }
}