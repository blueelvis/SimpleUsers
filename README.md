# SimpleUsers
A simple way to manage user accounts in php.

SimpleUsers is a library for managing user accounts in php. It can be hard to store userdata securely on your server. With this library all the difficult stuff is done for you. It will hash your passwords and encrypt the other data with a private key only you have.

## Installation

clone this repository:

~~~
$ git clone git@github.com:MarnixBouhuis/SimpleUsers.git
~~~

Copy the folder `SimpleUsers` to your webserver.

_And you are done, easy right!_

## Usage

Here's a quick example to demonstrate what a Slim template looks like:

~~~ php
<?php
require "SimpleUsers/SimpleUsers.php";                          //Include the library
~~~
~~~ php
$simpleusers = new SimpleUsers("MySQL", array(                  //Create a new instance and set the type to 'MySQL'
    "storageKey" => "xQQrhy9yENJpmBIH6MyQvANMbU6kEcPktoali4eGy9NFvPYeFmWaccXMSPVpB76Jtfpa6NUwLbpMyFYbnFDTW7294KLJdwg389HDbjRmk4mMHOBPIbSixjXIjD64RLXQOB2SqqT2bncMIMsaftpjeZDSpi35PMpZyWpsS", //This is your key, DON'T LOSE IT OTHERWISE YOUR DATA WILL BE USELESS. CHANGE THIS KEY TO A NEW RANDOM STRING AND MAKE SHURE IT'S LONG.
    "user" => "root",                                           //Your MySQL username
    "pass" => "example",                                        //Your MySQL password
    "address" => "localhost",                                   //Your MySQL server address
    "port" => "3306",                                           //Your MySQL server port
    "db" => "database",                                         //Your MySQL database name
    "table" => "userTable"                                      //Your MySQL table
));
~~~
~~~php
$simpleusers->addUser("user", "pass", [	//Adds a user with as username 'user', as password 'pass' and as data the array given, returns the userid of the new user if the operation succeeded or false if the operation failed.
	"some" => "random", 
	"data" => "here"
]);
~~~
~~~php
$simpleusers->removeUser(6); 					                //Removes the user with the id of 6, returns true if the operation succeeded or false if the operation failed
~~~
~~~php
if($simpleusers->validateLogin("user", "pass")){                //Validate the password of a user, returns true if the password is correct or false if the password isn't correct
	//The password is correct!
}else{
	//The password is incorrect.
}
~~~
~~~php
echo $simpleusers->getUserId("testuser4"); 	                    //Gets the userid of a user by name, returns the userid if the operation succeeded or false if the operation failed
~~~
~~~php
var_dump($simpleusers->getUserData(4));		               	    //Gets data stored with the user with the id 4, returns the data of the user if the operation succeeded or false if the operation failed
~~~
~~~php
$simpleusers->setUserData(4, "somekey", "somevalue");	        //Sets data for a user with the id 4, returns true if the operation succeeded or false if the operation failed
~~~
~~~php
if($simpleusers->userExists("user")){                           //Test if a user exists with the name 'user'
    //User exists
}else{
    //User doesn't exist
}
~~~
~~~php
$simpleusers->setupMySQLTable();				                //Setsup the table for storing data, returns true if the operation succeeded or false if the operation failed. MAKE SHURE THE TABLE DOESN'T EXISTS BEFORE CREATING ONE WITH THIS FUNCTION

?>
~~~

## Examples

_Please view the `examples` folder in this repository._

## Contributing

If you want to help this project, follow the steps below.

~~~
$ git clone git@github.com:MarnixBouhuis/SimpleUsers.git
~~~

Add your new features/fixes and submit a pull request.

If you want to improve the documentation, please submit an issue with the title: `[DOCUMENTATION IMPROVEMENT] subject` (Replace 'subject' with your subject :D )

## Discussions

[![Gitter](https://badges.gitter.im/MarnixBouhuis/SimpleUsers.svg)](https://gitter.im/MarnixBouhuis/SimpleUsers?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge) <- click :)

## License

SimpleUsers is released under the [GNU General Public License v3.0](http://choosealicense.com/licenses/gpl-3.0/).
View the `LICENSE` file for more info

## Author

* [Marnix Bouhuis](https://github.com/MarnixBouhuis)


_The end :D_
