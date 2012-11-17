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
     * @param array $transitions
     */
    public function addEvent($name, array $transitions);

    /**
     * set the default state for the machine
     * @param string $state
     */
    public function setDefaultState($state);

}
