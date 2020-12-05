<?php
include_once 'EventEmitter.php';
require_once 'utest.lib.php';

$evt = new EventEmitter();

$flag = '';

define('MSG0','start called');
define('MSG1','start called another time');
define('MSG2','parms: ');
define('MSG3','once called');
define('MSG4','second once called');

$newListener = (object)[
    'emitted'   => false,
    'event'     => null
];
$evt->once('newListener',function ($event,$listener) use (&$newListener) { $newListener->emitted = true; $newListener->event=$event; });

$evt->on('start',function() use(&$flag) {
    $flag .= MSG0;
});

$id_to_off = $evt->on('start',function() use(&$flag) {
    $flag .= MSG1;
});

$evt->on('parms',function(&$p) use(&$flag) {
    $p++;
    $flag .= MSG2.$p;
});

$evt->once('once',function() use(&$flag) {
    $flag .= MSG3;
});

$evt->once('once',function() use(&$flag) {
    $flag .= MSG4;
});

$parm = 0;

try {

TEST('Testing `newListener` was emitted');
    equal($newListener->emitted,true)
        ? DONE('it should have emitted `newListener`')
        : FAIL('it should have emitted `newListener` but found: "false"'); 
    equal($newListener->event,'start','it should have emitted `newListener` with the event name parameter');

TEST('Testing `listenerCount`');
    $count = $evt->listenerCount('start');
    equal($count,2,'it should have counted 2 `onstart`');
    $count = $evt->listenerCount('unknown_event');
    equal($count,0,'it should have counted 0 `unknown_event`');        

TEST('Testing emit on 2 `start` event');
    $flag = '';
    $evt->emit('start');
    equal($flag,MSG0.MSG1,'it should have called `onstart`'); 
        
TEST('Testing emit with `parms` event and reference parameters');
    $flag = '';
    $evt->emit('parms',array(&$parm));
    equal($flag,MSG2.'1','it should have called `onparms`'); 
    $flag = '';
    $evt->emit('parms',array(&$parm));
    equal($flag,MSG2.'2','it should have called `onparms`'); 
    
TEST('Testing emit with `once` event');
    $flag = '';
    $evt->emit('once');
    equal($flag,MSG3.MSG4,'it should have called `ononce`'); 
    $flag = '';
    $evt->emit('once');
    equal($flag,'','it should not have called `ononce`'); 

TEST('Testing off on 1 `start` event');
    $flag = '';
    $evt->off($id_to_off);
    $evt->emit('start');
    equal($flag,MSG0,'it should have called `onStart`'); 

TEST('Testing prependListener `sentence` event');
    $sentence = [];
    $evt->on('sentence',function(&$s){ $s[] = 'verbe'; });
    $evt->on('sentence',function(&$s){ $s[] = 'complément'; });
    $evt->prependListener('sentence',function(&$s){ $s[] = 'sujet'; });
    $evt->emit('sentence',array(&$sentence));
    equal(implode(' ',$sentence),'sujet verbe complément','it should have called `sentence` with prepend'); 

TEST('Testing eventNames()');
    $names = $evt->eventNames();
    sort($names);
    equal(implode(' ',$names),'newListener once parms sentence start','it should have called `sentence` with prepend'); 

function the_warning ($i,$n)
{
    return "MaxListenersExceededWarning: Possible EventEmitter memory leak detected. $i `$n` listeners added. Use emitter.setMaxListeners() to increase limit";
}

TEST('Testing max listeners');
    equal(EventEmitter::$defaultMaxListeners,10,'it should have 10 `default max listeners`');

    $warning  = '';
    error_reporting(E_USER_WARNING);
    set_error_handler(function( $errno, $errstr, $errfile, $errline ) use ( &$warning ) {
        if ( ! (error_reporting() & $errno) ) return;
        if ( $errno === E_USER_WARNING ) $warning = $errstr;
        return true;
    }, E_USER_WARNING);

    $expected = the_warning(3,'nop');
    $warning  = '';
    EventEmitter::$defaultMaxListeners = 2;
    $evt->addListener('nop',function(){});
    $evt->addListener('nop',function(){});
    $evt->addListener('nop',function(){});
    EventEmitter::$defaultMaxListeners = 10;
    equal($warning,$expected,'it should raise `MaxListenersExceededWarning` when defaultMaxListeners reached');

    $expected = the_warning(4,'nap');
    $warning  = '';
    $evt->setMaxListeners(3);
    $evt->addListener('nap',function(){});
    $evt->addListener('nap',function(){});
    $evt->addListener('nap',function(){});
    $evt->addListener('nap',function(){});
    equal($warning,$expected,'it should raise a `MaxListenersExceededWarning` when setMaxListeners reached');

TEST('Testing warned state');        
    $expected = '';
    $warning  = '';
    $evt->addListener('nap',function(){});
    equal($warning,$expected,'it should raise nothing that have already been raised');

    $expected = '';
    $warning  = '';
    $evt->setMaxListeners(4);
    $evt->addListener('nap',function(){});
    equal($warning,$expected,'it should not raise a `MaxListenersExceededWarning` after a change');

TEST('Testing autoRefreshWarnings');        
    $expected = the_warning(7,'nap');
    $warning  = '';
    $evt->autoRefreshWarnings = true;
    $evt->setMaxListeners(6);
    $evt->addListener('nap',function(){});
    equal($warning,$expected,'it should raise a `MaxListenersExceededWarning` after autoRefreshWarnings was sets to true');

TEST('Testing InvalidArgumentException');        
    $expected = 'EventEmitter::setMaxListeners : max should be greater or equal to zero.';
    $message  = '';
    try {
        $evt->setMaxListeners(-1);
    } catch ( InvalidArgumentException $e ) { $message = $e->getMessage(); }
    equal($message,$expected,'it should raise an `InvalidArgumentException`');

OVER();

} catch (Exception $e) { echo RED($e->getMessage()),PHP_EOL; }