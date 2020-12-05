<?php

class EventEmitter
{
    const version = '1.0.0';

    static  $defaultMaxListeners = 10;
    private $maxListener = null;

    private $events;

    public $autoRefreshWarnings = false;
    public $enableWarnings = true;

    function __construct ()
    {
        $this->events = array();
        $this->maxListener = null;
        $this->autoRefreshWarnings = false;
        $this->enableWarnings = true;
    }

    function addListener ( string $name, callable $func )/* : string */ 
    {
        return $this->on($name,$func);
    }

    function emit ( string $name, array $args=null )/* : void */
    {
        if ( ! key_exists($name,$this->events) ) return;
        foreach ( $this->events[$name] as $uid => & $event )
        {
            if ( ! is_array($event) ) continue;
            if ( $args )
                call_user_func_array( $event['func'], $args );
            else
                call_user_func( $event['func'] );

            if ( $event['once'] ) $this->off($uid);
        }
    }

    function eventNames ()/* : array */
    {
        return array_keys($this->events);
    }

    function getMaxListeners ()/* : int */
    {
        return $this->maxListener === null ? EventEmitter::$defaultMaxListeners : $this->maxListener;
    }

    function listenerCount ( string $name='' )/* : int */
    {
        $count = 0;
        if ( key_exists($name,$this->events) ) 
            foreach ( $this->events[$name] as $event ) if (is_array($event)) $count++;
        return $count;
    }

    function listeners ( string $name='' )/* : array */
    {
        $listeners = array();
        if ( key_exists($name,$this->events) ) $listeners = array_merge($listeners,$this->events[$name]);
        return $listeners;
    }

    function off ( string $id )/* : void */
    {
        list($name,$uid) = explode('$',$id,2);
        if ( ! isset($this->events[$name]) && ! isset($this->events[$name][$id]) ) return;
        unset($this->events[$name][$id]);
    }

    function on ( string $name, callable $func )/* : string */
    {
        $this->emit('newListener',array($name,& $func));
        return $this->store($name, array('func'=> & $func,'once'=>false));
    }

    function once ( string $name, callable $func )/* : string */
    {
        $this->emit('newListener',array($name,& $func));
        return $this->store($name, array('func'=> & $func,'once'=>true));
    }

    function prependListener ( string $name, callable $func )/* : string */
    {
        $this->emit('newListener',array($name,& $func));
        return $this->store($name, array('func'=> & $func,'once'=>false), true);
    }

    function prependOnceListener ( string $name, callable $func )/* : string */
    {
        $this->emit('newListener',array($name,& $func));
        return $this->store($name, array('func'=> & $func,'once'=>true), true);
    }

    function refreshWarnings ()
    {
        foreach ( array_keys($this->events) as $key )
            if ( key_exists('warned',$this->events[$key]) )
                $this->events[$key]['warned'] = false;
    }

    function removeListener( string $id )/* : void */
    {
        $this->off($id);
    }

    function setMaxListeners ( int $maxListener )/* : void */
    {
        if ( $this->maxListener == $maxListener ) return;

        if ( $maxListener < 0 )
            throw new InvalidArgumentException(__METHOD__.' : max should be greater or equal to zero.');

        $this->maxListener = $maxListener;

        if ( $this->autoRefreshWarnings ) $this->refreshWarnings();
    }

    private function memoryLeakWarning ( string $key )/* : void */
    {
        $maxListener = $this->getMaxListeners();
        $count =$this->listenerCount($key);
        if ( $maxListener > 0 && $count > $maxListener && ! $this->events[$key]['warned'] )
        {
            $msg = 'MaxListenersExceededWarning: Possible EventEmitter memory leak detected. '
                 . "$count `$key` listeners added. Use emitter.setMaxListeners() to increase limit";
            trigger_error($msg, E_USER_WARNING);
            $this->events[$key]['warned'] = true;
        }
    } 

    private function store ( string $key, array $values, bool $prepend=false )/* : string */
    {
        if ( ! isset($this->events[$key]) ) $this->events[$key] = array('warned'=>false);
        do { $id = uniqid($key.'$'); } while ( key_exists($id,$this->events[$key]) ); // No luck today ? :)
    
        if ( $prepend )
            $this->events[$key] = array_merge(array($id => $values),$this->events[$key]);
        else
            $this->events[$key][$id] = $values;

        if ( $this->enableWarnings ) $this->memoryLeakWarning($key);

        return $id;
    }
}