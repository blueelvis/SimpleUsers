<?php
require "SimpleUsers/SimpleUsers.php";

$simpleusers = new SimpleUsers("MySQL", array(
    "storageKey" => "xQQrhy9yENJpmBIH6MyQvANMbU6kEcPktoali4eGy9NFvPYeFmWaccXMSPVpB76Jtfpa6NUwLbpMyFYbnFDTW7294KLJdwg389HDbjRmk4mMHOBPIbSixjXIjD64RLXQOB2SqqT2bncMIMsaftpjeZDSpi35PMpZyWpsS",
    "user" => "root",
    "pass" => "example",
    "address" => "localhost",
    "port" => "3306",
    "db" => "database",
    "table" => "userTable"
));

if($simpleusers->userExists("user")){
  $newUserId = $simpleusers->addUser("user", "pass", []);
  echo "User created with id: " . $newUserId;
}else{
  $userId = $simpleusers->getUserId("user");
  if($simpleusers->removeUser($userId)){
    echo "Successfully deleted the user with id:" . $userId;
  }
}

?>
