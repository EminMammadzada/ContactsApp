<?php  
  /*
    First name, last name, email, and phone number are given from user input. If the login
    is unique, the inputted data will be associated with a new user.
  */
  
  $inData = getRequestInfo();
  // user input
  $firstName = $inData['firstName'];
  $lastName = $inData['lastName'];
  $login = $inData['login'];
  $password = $inData['password'];

  // establishes connection with mysqli, and errors out if the connection fails
  $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
  if ($conn->connect_error)
  {
    returnWithError($conn->connect_error);
  }
  // else if the sql session was opened without error
  else
  {
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Login=?");
    $stmt->bind_param("s", $login);
    $stmt->execute();

    $result = $stmt->get_result();

    // check if the login and password that the user chose is taken or not; give an error if the username is already taken
    if ($row = $result->fetch_assoc())
    {
      returnWithError("Username is taken. Please choose another one");
    }
    // if the login and password are unique 
    else
    {
      // insert the inputted data into a new user row
      $stmt2 = $conn->prepare("INSERT into Users (FirstName, LastName, Login, Password) VALUES(?,?,?,?)");
      $stmt2->bind_param("ssss", $firstName, $lastName, $login, $password);
      $stmt2->execute();
      $stmt2->close();

      // get the ID of the newly created user
      $stmt3 = $conn->prepare("SELECT * FROM Users WHERE Login=?");
      $stmt3->bind_param("s", $login);
      $stmt3->execute();
      $result2 = $stmt3->get_result();

      // if the user was successfully created, return the user's information
      if ($row2 = $result2->fetch_assoc())
      {
        returnWithInfo($row2['FirstName'], $row2['LastName'], $row2['Login'], $row2['ID']);
      }
      // if the user could not be created, throw an error
      else
      {
        returnWithError("User could not be created for some reason");
      }
      $stmt3->close();
    }
    $stmt->close();
    $conn->close();
  }

  function getRequestInfo()
  {
    return json_decode(file_get_contents('php:// input'), true);
  }

  function sendResultInfoAsJson($obj)
  {
    header('Content-type: application/json');
    echo $obj;
  }

  function returnWithError($err)
  {
    $retValue = '{"error":"' . $err . '"}';
    sendResultInfoAsJson($retValue);
  }

  function returnWithInfo($firstName, $lastName, $login, $id)
  {
    $retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","login":"' . $login . '","lastName":"' . $lastName . '","error":""}';
    sendResultInfoAsJson($retValue);
  }
?>
