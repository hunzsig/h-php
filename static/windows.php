<?php
function winSound($str)
{
    if (IS_WINDOW) @exec('mshta vbscript:createobject("sapi.spvoice").speak("' . $str . '")(window.close)');
}