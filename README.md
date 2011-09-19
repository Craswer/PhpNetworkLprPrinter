PhpNetworkLprPrinter - PHP Class to manage network printers via LPR standar (RFC 1179)
======================================================================================

Status
------

This project is very much an early work-in-progress. While it is usable, there are no guarantees 
that it will not break or cause unintended effects.


Getting started
---------------

Assuming you have downloaded the Class PhpNetworkLprPrinter.php, you can get started by including the
file in your program:

	include("PhpNetworkLprPrinter.php");
	
Somewhere in your code you need to initialize the network printer.
Here is an example of doing it with a printer located in 192.168.1.136:

	$lpr = new PhpNetworkLprPrinter("192.168.1.136");
	
Or if you prefer, with port:

	$lpr = new PhpNetworkLprPrinter("192.168.1.136", 515);
	
After initialize the printer, to print some text you need to write the following code:

	$lpr->printText("Hello worldd!"); 

Debugging
---------
If you like to debug the application, there is a function called "getDebug". 

	$array_debug = $lpr->getDebug();

This function return an array with the following structure:

	Array
	(
    [0] => Array
        (
            [message] => string
            [time] => timestamp
            [type] => message / error
        )
    [1] => Array
        (
            [message] => string
            [time] => timestamp
            [type] => message / error
        )
	....
	)
	
License
-------

Copyright 2011 Pedro Villena (craswer@gmail.com). 
	
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see http://www.gnu.org/licenses/