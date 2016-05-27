<?php

namespace omnilight\scheduling;

use yii\base\Application;
use yii\base\Component;


/**
 * Class Schedule
 */
class Schedule extends Component
{
    public $yiiExecutable;

    /**
     * All of the events on the schedule.
     *
     * @var Event[]
     */
    protected $_events = [];

    public function init()
    {
        $this->yiiExecutable = $this->yiiExecutable ?: basename(\Yii::$app->request->getScriptFile());
    }

    /**
     * Add a new callback event to the schedule.
     *
     * @param  string $callback
     * @param  array $parameters
     * @return Event
     */
    public function call($callback, array $parameters = [])
    {
        $this->_events[] = $event = new CallbackEvent($callback, $parameters);
        return $event;
    }

    /**
     * Add a new cli command event to the schedule.
     *
     * @param  string $command
     * @return Event
     */
    public function command($command)
    {
        return $this->exec(sprintf('%s %s %s', PHP_BINARY, $this->yiiExecutable, $command));
    }

    /**
     * Add a new command event to the schedule.
     *
     * @param  string $command
     * @return Event
     */
    public function exec($command)
    {
        $this->_events[] = $event = new Event($command);
        return $event;
    }

    public function getEvents()
    {
        return $this->_events;
    }

    /**
     * Get all of the events on the schedule that are due.
     *
     * @param \yii\base\Application $app
     * @return Event[]
     */
    public function dueEvents(Application $app)
    {
        return array_filter($this->_events, function (Event $event) use ($app) {
            return $event->isDue($app);
        });
    }
}
