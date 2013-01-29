# Finite-State Machine Library
================================

A simple PHP implementation of a [Finite-State Machine](http://en.wikipedia.org/wiki/Finite-state_machine)

## Installation
--------------

Use [Composer](http://getcomposer.org) to install this library in your project 

### Create your composer.json file

      {
          "require": {
              "thetwelvelabs/techne": "0.2.*@dev"
          }
      }

### Download composer into your application root

      $ curl -s http://getcomposer.org/installer | php

### Install your dependencies

      $ php composer.phar install

## Usage
---------

Let's use a light switch as a simple example.  
A light switch as two states: on and off. The state of a light switch is 
transitioned from one to the other by flipping the switch. We'll assume
that the initial state of the light switch is 'off' 

### Define your FSM

      $machine = new StateMachine\FiniteStateMachine();
      $machine->setInitialState('off');

### Define the transitions

      $turnOff = new StateMachine\Transition('on', 'off');
      $turnOn = new StateMachine\Transition('off', 'on');
        
### Add a guard to the turnOn transition

      // flipping the switch on requires electricity
      $hasElectricity = true;
      $turnOn->before(function() use ($hasElectricity) {
          return $hasElectricity ? true : false;
      });

## Define the events

      $machine->addEvent('flip', array($turnOn, $turnOff));

### Transition from off to on

      $machine->flip();  
      echo $machine->getCurrentState();
      // prints 'on'  
      
### Transition back to off

      $machine->flip();  
      echo $machine->getCurrentState();
      // prints 'off'  

      
