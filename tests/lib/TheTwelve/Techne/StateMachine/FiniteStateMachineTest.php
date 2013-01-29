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
        $this->assertTrue($machine->is('off'));

        $machine->flip();
        $this->assertEquals('on', $machine->getCurrentState(), 'Could not turn lights on');
        $this->assertTrue($machine->is('on'));

        $machine->flip();
        $this->assertEquals('off', $machine->getCurrentState(), 'Could not turn lights off');
		$this->assertTrue($machine->is('off'));

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
        $this->assertTrue($machine->is('parked'));

        // cannot go from parked to driving
        $this->setExpectedException('TheTwelve\Techne\InvalidTransitionException');
        $machine->drive();

        $machine->start();
        $this->assertEquals('idling', $machine->getCurrentState());
        $this->assertTrue($machine->is('idling'));

        $machine->park();
        $this->assertEquals('parked', $machine->getCurrentState());
        $this->assertTrue($machine->is('parked'));

        $machine->start();
        $machine->drive();
        $this->assertEquals('driving', $machine->getCurrentState());
        $this->assertTrue($machine->is('driving'));

        $machine->stop();
        $this->assertEquals('idling', $machine->getCurrentState());
        $this->assertTrue($machine->is('idling'));

        $machine->park();
        $this->assertEquals('parked', $machine->getCurrentState());
        $this->assertTrue($machine->is('parked'));

        $this->setExpectedException('TheTwelve\Techne\InvalidEventException');
        $machine->foo();

    }

    public function testEating()
    {

    	// let's say humans have 4 states as a result of eating
    	// hungry, satisfied, full, and sick

        $eatOnce = new StateMachine\Transition('hungry', 'satisfied');
        $eatTwice = new StateMachine\Transition('satisfied', 'full');
        $eatThrice = new StateMachine\Transition('full', 'sick');
        $rest = new StateMachine\Transition(array('satisfied', 'full', 'sick'), 'hungry');

        $machine = new StateMachine\FiniteStateMachine();
        $machine->addEvent('eat', array($eatOnce, $eatTwice, $eatThrice));
        $machine->addEvent('rest', $rest);
        $machine->setInitialState('hungry');

        $this->assertEquals('hungry', $machine->getCurrentState());
        $this->assertTrue($machine->is('hungry'));

        $machine->eat();
        $this->assertEquals('satisfied', $machine->getCurrentState());
        $this->assertTrue($machine->is('satisfied'));

        $machine->rest();
        $this->assertEquals('hungry', $machine->getCurrentState());
        $this->assertTrue($machine->is('hungry'));

        $machine->eat();
        $machine->eat();
        $this->assertEquals('full', $machine->getCurrentState());
        $this->assertTrue($machine->is('full'));

        $machine->rest();
        $this->assertEquals('hungry', $machine->getCurrentState());
        $this->assertTrue($machine->is('hungry'));

        $machine->eat();
        $machine->eat();
        $machine->eat();
        $this->assertEquals('sick', $machine->getCurrentState());
        $this->assertTrue($machine->is('sick'));

        $machine->rest();
        $this->assertEquals('hungry', $machine->getCurrentState());
        $this->assertTrue($machine->is('hungry'));

        // Issue #2 - Bug with multiple source states
        $machine->eat();
        $machine->eat();
        $this->assertEquals('full', $machine->getCurrentState());
        $this->assertTrue($machine->is('full'));

        $currentState = $machine->getCurrentState();

        $rest->before(function() {
            return false;
        });

        $machine->rest();
        $this->assertFalse(is_array($machine->getCurrentState()));
        $this->assertEquals($currentState, $machine->getCurrentState());

    }

}
