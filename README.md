# EventEmitter.php

Another EventEmitter class in PHP that reproduce the javascript EventEmitter, with some more features.

# Table of Contents

- [EventEmitter.php](#eventemitterphp)
- [Table of Contents](#table-of-contents)
- [Install](#install)
- [EventEmitter class](#eventemitter-class)
  - [Fields](#fields)
  - [Methods](#methods)
- [Testing](#testing)
- [License](#license)

# Install

Clone or unzip.

# EventEmitter class

Create any instance of this class wherever you need. 

The listners are stored in order they are sets. Use the prepend methods to store a listener from the top.

## Fields

| Name | Type | Description |
|:----------------|:------------:|:------------------------------------------------------|
| version | `string` | The class version. |
| defaultMaxListeners | `integer` | Maximum of listeners before warning. |
| autoRefreshWarnings | `boolean`| Enable the warning refresh on changing the listeners size. _false_ |
| enableWarnings | `boolean`| Enable the warning when listeners allocation exceed. _true_ |

## Methods

* ____construct( void )__

  _Create an instance._
  
* __addListener ( string $name, callable $func ) : string__

  _Add a listner and returns its id._
  
    * name
    
      Type: `string`
      
      _The name of the listener._

    * func
    
      Type: `callable`
      
      _A callback to fire attached to this listener._ 

  > See on()

* __emit ( string $name, array $args=null ) : void__

  _Fire a listener._
  
    * name
    
      Type: `string`
      
      _The name of the listener._

    * args
    
      Type: `array`
      
      _Some optionals argunents for the callback._

* __eventNames () : array__

  _Retrieve a list of all the listner names._
  
* __getMaxListeners () : int__

  _Retrieve the maximum number listeners for this instance._

* __listenerCount ( string $name='' ) : int__

  _Retrieve the number of listener attached to an event._
  
    * name
    
      Type: `string`
      
      _The name of the listener._

* __listeners ( string $name='' ) : array__

  _Retrieve the a list of listeners attached to an event._
  
    * name
    
      Type: `string`
      
      _The name of the listener._

* __off ( string $id ) : void__

  _Remove a listener according to its id._
  
    * id
    
      Type: `string`
      
      _The id of the listener._ 

   > See removeListener()      

* __on ( string $name, callable $func ) : string__

  _Add a listner and returns its id._

    * name
    
      Type: `string`
      
      _The name of the listener._

    * func
    
      Type: `callable`
      
      _A callback to fire attached to this listener._ 

   > See addListener()

* __once ( string $name, callable $func ) : string__

  _Add a listner and returns its id. This listener will be fired only once and removed._

    * name
    
      Type: `string`
      
      _The name of the listener._

    * func
    
      Type: `callable`
      
      _A callback to fire attached to this listener._

* __prependListener ( string $name, callable $func ) : string__

  _Add a listner **on top of the list** and returns its id._

    * name
    
      Type: `string`
      
      _The name of the listener._

    * func
    
      Type: `callable`
      
      _A callback to fire attached to this listener._

* __prependOnceListener ( string $name, callable $func ) : string__

  _Add a listner **on top of the list** and returns its id. This listener will be fired only once and removed._

    * name
    
      Type: `string`
      
      _The name of the listener._

    * func
    
      Type: `callable`
      
      _A callback to fire attached to this listener._

* __refreshWarnings () : void__

  _Clear the warning flag from the listeners. Indeed, a warning occurs only one time._

* __removeListener ( string $id ) : void__

  _Remove a listener according to its id._
  
    * id
    
      Type: `string`
      
      _The id of the listener._ 

   > See off()      

* __setMaxListeners ( int $maxListener ) : void__

  _Change the maximum listeners that this instance can handle._
  
    * maxListener
    
      Type: `integer`
      
      _The number of maximum listeners._ 

# Testing

   Unit tests are made with my simpliest and light library, `'micron test'` _utest.lib.php_

```bash
$ php -f tests/EventEmitter.test.php
```

# License

[MIT © guitarneck](./LICENSE)