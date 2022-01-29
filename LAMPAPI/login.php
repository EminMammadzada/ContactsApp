<?php
  /*
    Takes in the user inputted login and password and returns the user's information and updates
    the last time logged in if the login and password parameters match a user, errors out otherwise
  */

  $inData = getRequestInfo();
  // user input
  $login = $inData['login'];
  $password = $inData['password'];

  $userID = 0;
  $firstName = "";
  $lastName = "";

  // establishes connection with mysqli, and errors out if the connection fails
  $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
  if ($conn->connect_error)
  {
    returnWithError($conn->connect_error);
  }
  // else if the sql session was opened without error
  else
  {
    // checks if there is a user with the specified login and password
    $stmt = $conn->prepare("SELECT ID, FirstName, LastName FROM Users WHERE Login=? AND Password=?");
    $stmt->bind_param("ss", $login, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // if the user exists print the info
    if ($row = $result->fetch_assoc())
    {
      $sql = "UPDATE Users SET DateLastLoggedIn=now() WHERE Login='$login'";
      $conn->query($sql);
      returnWithInfo($row['FirstName'], $row['LastName'], $login, $row['ID']);
    }
    // if the user does not exist, error out
    else
    {
      returnWithError("No records found");
    }

    $stmt->close();
    $conn->close();
  }

  function getRequestInfo()
  {
  return json_decode(file_get_contents('php://input'), true);
  }

  function sendResultInfoAsJson($obj)
  {
    header('Content-type: application/json');
    echo $obj;
  }

  function returnWithError($err)
  {
    $retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
    sendResultInfoAsJson($retValue);
  }

  function returnWithInfo($firstName, $lastName, $login, $userID)
  {
    $retValue = '{"id":' . $userID . ',"firstName":"' . $firstName . '","login":"' . $login . '","lastName":"' . $lastName . '","error":""}';
    sendResultInfoAsJson($retValue);
  }
?>
