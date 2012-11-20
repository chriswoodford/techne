<?php

namespace TheTwelve\Techne\StateMachine;

use TheTwelve\Techne;

class FiniteStateMachine implements Techne\StateMachine
{

    /**
     * the current state of this machine
     * @var TheTwelve\Techne\State
     */
    protected $state;

    /**
     * a list of available events
     * @var unknown_type
     */
    protected $events;

    /**
     * (non-PHPdoc)
     * @see TheTwelve\Techne.StateMachine::getCurrentState()
     */
    public function getCurrentState()
    {

        return $this->state;

    }

    /**
     * returns true if the FSM's current state is the
     * same as the supplied state
     * @param TheTwelve\Techne\State $state
     */
    public function is($state)
    {

    	return $this->state == $state;

    }

    /**
     * (non-PHPdoc)
     * @see TheTwelve\Techne.StateMachine::addEvent()
     */
    public function addEvent($name, $transitions)
    {

    	if ($transitions instanceof \TheTwelve\Techne\Transition) {
    		$transitions = array($transitions);
    	}

        $this->events[$name] = $transitions;
        return $this;

    }

    /**
     * (non-PHPdoc)
     * @see TheTwelve\Techne.StateMachine::setInitialState()
     */
    public function setInitialState($state)
    {

        $this->state = $state;
        return $this;

    }

    /**
     * catch messages passed to this machine and treat them as events
     * @param string $name
     * @param array $arguments
     * @throws InvalidEventException
     * @throws InvalidTransitionException
     */
    public function __call($name, $arguments)
    {

        if (!array_key_exists($name, $this->events)) {
            throw new Techne\InvalidEventException(
                'Event [' . $name . '] does not exist'
            );
        }

        $transitions = $this->events[$name];
        $transition = null;

        foreach ($transitions as $t) {

        	$transition = $t->initialStateIs($this->state)
        		? $t : null;

            if (!is_null($transition)) {
            	break;
            }

        }

        if (!$transition) {
            throw new Techne\InvalidTransitionException(
                'Machine cannot transition from '
                . '[' . $this->state . '] after [' . $name . ']'
            );
        }

        $this->state = $transition->process();

    }

}
