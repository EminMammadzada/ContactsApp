<?php

  // TODO: account for when there is no recordID

  // scraped info
  $inData = getRequestInfo();
  $firstName = $inData["firstName"];
  $lastName = $inData["lastName"];
  $email = $inData["email"];
  $phone = $inData["phone"];

  // session info
  $userID = $inData["id"]; // logged in user id, NOT record id

  // grab the user id from the session and scrape the contact info from the screen

  $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
  if ($conn->connect_error)
  {
    returnWithError( $conn->connect_error );
  }
  else
  {
    $stmt = $conn->prepare("SELECT ID FROM Records WHERE FirstName LIKE '%?%' AND LastName LIKE '%?%' AND Email LIKE '%?%' AND Phone LIKE '%?%' AND UserID=?");
    $stmt->bind_param("sssss", $firstName, $lastName, $email, $phone, $userID);
    $stmt->execute();

    $result = $stmt->get_result();

    // FIXME: this is not in a json format \/\/\/\/\/
    sendResultInfoAsJson($result->fetch_assoc());

    //
    // // if there is such contact, then delete it
    // if ($row = $result->fetch_assoc())
    // {
    //   $stmt2 =  $conn->prepare("UPDATE Records SET FirstName=? AND LastName=? AND Email=? AND Phone=? WHERE ID=? AND UserID=?");
    //   $stmt2->bind_param("ssss", $firstName, $lastName, $email, $phone, $recordID, $userID);
    //   $stmt2->execute();
    //
    //   returnWithInfo("");
    //   $stmt2->close();
    // }

    // if contact does not exist, then error out
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

  function returnWithInfo($recordID)
  {
    $retValue = '{"recordID":' . $recordID . '","error":""}';
    sendResultInfoAsJson($retValue);
  }

?>