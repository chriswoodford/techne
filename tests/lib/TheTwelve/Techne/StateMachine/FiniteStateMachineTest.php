<?php

use TheTwelve\Techne\StateMachine\Transition;
use TheTwelve\Techne\StateMachine;

class TheTwelve_Techne_StateMachine_FiniteStateMachineTest
    extends \PHPUnit_Framework_TestCase
{

    public function testLightSwitch()
    {

        // light switches have 2 states: on and off
        $turnOff = new StateMachine\Transition('on', 'off');
        $turnOn = new StateMachine\Transition('off', 'on');

        // flipping the switch on requires electricity
        $hasElectricity = true;
        $turnOn->before(function() use ($hasElectricity) {
        	return $hasElectricity ? true : false;
        });

        $machine = new StateMachine\FiniteStateMachine();
        $machine->addEvent('flip', array($turnOn, $turnOff));
        $machine->setInitialState('off');

        $this->assertEquals('off', $machine->getCurrentState(), 'Incorrect initial state for light switch');

        $machine->flip();
        $this->assertEquals('on', $machine->getCurrentState(), 'Could not turn lights on');

        $machine->flip();
        $this->assertEquals('off', $machine->getCurrentState(), 'Could not turn lights off');

        $this->setExpectedException('TheTwelve\Techne\InvalidEventException');
        $machine->foo();

    }

    public function testCar()
    {

        // car has 3 states: parked, idling, driving
        // transitions:
        //    parked -> idling
        //    idling -> parked
        //    idling -> driving
        //    driving -> idling

        $startCar = new StateMachine\Transition('parked', 'idling');

        // car need gas in order to start
        $hasGas = true;
        $startCar->before(function() use ($hasGas) {
			return $hasGas ? true : false;
        });

        $driveCar = new StateMachine\Transition('idling', 'driving');
        $stopCar = new StateMachine\Transition('driving', 'idling');
        $parkCar = new StateMachine\Transition('idling', 'parked');

        $machine = new StateMachine\FiniteStateMachine();
        $machine->addEvent('start', $startCar);
        $machine->addEvent('drive', $driveCar);
        $machine->addEvent('stop', $stopCar);
        $machine->addEvent('park', $parkCar);
        $machine->setInitialState('parked');

        $this->assertEquals('parked', $machine->getCurrentState());

        // cannot go from parked to driving
        $this->setExpectedException('TheTwelve\Techne\InvalidTransitionException');
        $machine->drive();

        $machine->start();
        $this->assertEquals('idling', $machine->getCurrentState());

        $machine->park();
        $this->assertEquals('parked', $machine->getCurrentState());

        $machine->start();
        $machine->drive();
        $this->assertEquals('driving', $machine->getCurrentState());

        $machine->stop();
        $this->assertEquals('idling', $machine->getCurrentState());

        $machine->park();
        $this->assertEquals('parked', $machine->getCurrentState());

        $this->setExpectedException('TheTwelve\Techne\InvalidEventException');
        $machine->foo();

    }

}
