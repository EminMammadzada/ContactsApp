<?php
  /*
    User ID is cached and the first name, last name, email, and phone number 
    are given from user input. If the user is registered, this adds a new contact
    with the specified user input as its fields, throwing an error iff the contact
    is identical to another contact of the cached user
  */

  $inData = getRequestInfo();
  // user input
  $firstName = $inData["firstName"];
  $lastName = $inData["lastName"];
  $email = $inData["email"];
  $phone = $inData["phone"];
  // cached data
  $userID = $inData["userID"];

  // establishes connection with mysqli, and errors out if the connection fails
  $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
  if ($conn->connect_error) 
  {
    returnWithError($conn->connect_error);
  }
  // else if the sql session was opened without error
  else
  {
    $stmt = $conn->prepare("SELECT * FROM Users WHERE ID=?");
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    // if we are adding contacts for registered users
    if ($row = $result->fetch_assoc())
    {
      // check if the user already has a contact with given credentials
      $stmt2 = $conn->prepare("SELECT * FROM Records WHERE FirstName=? AND LastName=? AND Email=? AND Phone=? AND UserID=?");
      $stmt2->bind_param("sssss", $firstName, $lastName, $email, $phone, $userID);
      $stmt2->execute();
      $result2 = $stmt2->get_result();

      // if there is a contact with given credentials, throw an error
      if ($row2 = $result2->fetch_assoc())
      {
        returnWithError("You already have this contact");
      }
      // if the record being added is new, actually insert the data
      else
      {
        $stmt3 = $conn->prepare("INSERT into Records (UserID,FirstName,LastName,Email,Phone) VALUES(?,?,?,?,?)");
        $stmt3->bind_param("sssss", $userID, $firstName, $lastName, $email, $phone);
        $stmt3->execute();
        $stmt3->close();

        returnWithInfo($firstName, $lastName, $email, $phone, $userID);
      }
    }
    // if the userID does not match any records, throw an error
    else
    {
      returnWithError("There is no such user. You cannot add contacts for an unexisting user");
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
    $retValue = '{"error":"' . $err . '"}';
    sendResultInfoAsJson($retValue);
  }

  function returnWithInfo($firstName, $lastName, $email, $phone, $id)
  {
    $retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","email":"' . $email . '","phone":"' . $phone . '","error":""}';
    sendResultInfoAsJson($retValue);
  }
?>