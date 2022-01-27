<?php

  /*
    Record ID and User ID are given from variables, and the first name, last name, email, and
    phone number are given from user input. This takes the user input and applies it to the contact
    with the specified Record ID.
  */

  // scraped info
  $inData = getRequestInfo();
  $firstName = $inData["firstName"];
  $lastName = $inData["lastName"];
  $email = $inData["email"];
  $phone = $inData["phone"];

  // stored info(hidden variable)
  $userID = $inData["id"]; // logged in user id, NOT record id
  $recordID = $inData["recordID"];

  $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
  if ($conn->connect_error)
  {
    returnWithError( $conn->connect_error );
  }
  else
  {
    $stmt = $conn->prepare("SELECT * FROM Records WHERE ID=? AND UserID=?");
    $stmt->bind_param("ss", $recordID, $userID);
    $stmt->execute();

    $result = $stmt->get_result();

    // if there is such contact, then update it with the user's input
    if ($row = $result->fetch_assoc())
    {
      // sets up the command for updating the contact with the specific recordID and userID
      $stmt2 =  $conn->prepare("UPDATE Records SET FirstName=? AND LastName=? AND Email=? AND Phone=? WHERE ID=? AND UserID=?");
      $stmt2->bind_param("ssssss", $firstName, $lastName, $email, $phone, $recordID, $userID);
      $stmt2->execute();
      returnWithError("");
      $stmt2->close();
    }
    // the contact does not exist, error out
    else
    {
      returnWithError("No such contact exists");
    }

    $stmt->close();
    $conn->close();
  }

  function getRequestInfo()
  {
    return json_decode(file_get_contents('php://input'), true);
  }

  function sendResultInfoAsJson( $obj )
  {
    header('Content-type: application/json');
    echo $obj;
  }

  function returnWithError( $err )
  {
    $retValue = '{"error":"' . $err . '"}';
    sendResultInfoAsJson( $retValue );
  }

  function returnWithInfo( $firstName, $lastName, $id)
  {
    $retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
    sendResultInfoAsJson( $retValue );
  }

?>