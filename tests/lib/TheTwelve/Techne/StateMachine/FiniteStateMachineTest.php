<?php

use TheTwelve\Techne\StateMachine;

class TheTwelve_Techne_StateMachine_FiniteStateMachineTest
    extends \PHPUnit_Framework_TestCase
{

    public function testLightSwitch()
    {
    	
    	// light switches have 2 states: on and off
    	
    	$machine = new StateMachine\FiniteStateMachine();
    	$machine->addEvent('flip', array('on' => 'off', 'off' => 'on'));
    	$machine->setDefaultState('off');
    	
    	$this->assertEquals('off', $machine->getCurrentState());
    	
    	$machine->flip();
    	$this->assertEquals('on', $machine->getCurrentState());
    	
    	$machine->flip();
    	$this->assertEquals('off', $machine->getCurrentState());
    	
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
    	
    	$machine = new StateMachine\FiniteStateMachine();
    	$machine->addEvent('start', array('parked' => 'idling'));
    	$machine->addEvent('drive', array('idling' => 'driving'));
    	$machine->addEvent('stop', array('driving' => 'idling'));
    	$machine->addEvent('park', array('idling' => 'parked'));
    	$machine->setDefaultState('parked');
    	
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
