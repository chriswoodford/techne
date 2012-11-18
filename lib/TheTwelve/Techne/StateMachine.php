<?php

namespace TheTwelve\Techne;

interface StateMachine
{

    /**
     * get the current state
     * @return TheTwelve\Techne\State
     */
    public function getCurrentState();

    /**
     * add an event and the allowed transitions
     * @param string $name
     * @param array|TheTwelve\Techne\Transition $transitions
     */
    public function addEvent($name, $transitions);

    /**
     * set the initial state for the machine
     * @param string $state
     */
    public function setInitialState($state);

}
