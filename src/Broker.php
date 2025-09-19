<?php declare(strict_types=1);

namespace AtkPubSubDemo;

use Atk4\Core\HookTrait;
use Closure;

class Broker
{
    use HookTrait;

    /** ---------------------------------------------- Singleton Code  ---------------------------------------------  */

    protected static ?Broker $instance = null;

    /**
     * @return Broker
     */
    public static function get(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }


    /** -------------------------------------------- Brokering events ----------------------------------------------  */

    /**
     * Simple Wrapper for $this->hook to keep pubsub naming
     * @param string $hookSpot
     * @param array $parametersForCallback
     * @return mixed
     */
    public function publish(string $hookSpot, array $parametersForCallback): mixed
    {
        return $this->hook($hookSpot, $parametersForCallback);
    }

    /**
     * Subscribe to a hook spot. Call this function from any Class that should act upon an event like Model after insert.
     * $fx receives the same parameters as passed to publish(). So, If you subscribe to Model::HOOK_AFTER_SAVE,
     * the $fx has the same 2 parameters as if adding a hook directly to this hook spot: the Entity and $isUpdate
     *
     * @param string $hookSpot
     * @param Closure $fx
     * @return void
     */
    public function subscribe(string $hookSpot, Closure $fx): void
    {
        $this->onHookShort($hookSpot, $fx);
    }
}