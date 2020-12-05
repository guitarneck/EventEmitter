<?php
if(!defined('__UTEST_LIB__')):
     define('__UTEST_LIB__',1);

define('COL_END'    ,"\033[0m");
define('COL_WHITE'  ,"\033[1;37m");
define('COL_RED'    ,"\033[31m");
define('COL_GREEN'  ,"\033[32m");
define('COL_CYAN'   ,"\033[36m");

define('TICKS', '✔');
define('CROSS', '✘');

function utest_stringinfy ( $v )
{
    if ( is_bool($v) ) return $v ? 'true' : 'false';
    return var_export($v,true);
}

function WHITE  ($t) { return COL_WHITE.$t.COL_END; }
function RED    ($t) { return COL_RED.$t.COL_END; }
function GREEN  ($t) { return COL_GREEN.$t.COL_END; }

$RESULT = [
    'done'  => 0,
    'fail'  => 0
];
function TEST ($t) {echo PHP_EOL,WHITE($t),PHP_EOL;}
function FAIL ($t) {global $RESULT; echo '    ',RED(CROSS),' ',$t,PHP_EOL; $RESULT['fail']++;}
function DONE ($t) {global $RESULT; echo '    ',GREEN(TICKS),' ',$t,PHP_EOL; $RESULT['done']++;}
function OVER ()   {global $RESULT; $d=$RESULT['done'];$f=$RESULT['fail'];$n=PHP_EOL;$t='  ';echo $n,$t,'done : ',GREEN($d),$n,$t,'fail : ',($f?RED($f):'0'),$n,$t,'total: ',$f+$d,$n,$n;}

function equal($v,$w,$m=null) { return $m==null ? $v == $w : ($v == $w ? DONE($m) : FAIL("$m, but found : `".utest_stringinfy($v)."`")); }
function notNull($v,$m=null) { return $m==null ? $v !== null : ($v !== null ? DONE($m) : FAIL("$m, but found : `null`")); }

endif;