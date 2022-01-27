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
  if ($conn->connect_error){
    returnWithError( $conn->connect_error );
  }

  else{
    $stmt = $conn->prepare("SELECT ID FROM Records WHERE FirstName=? AND LastName=? AND Email=? AND Phone=? AND UserID=?");
    $stmt->bind_param("sssss", $firstName, $lastName, $email, $phone, $userID);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()){
       returnWithInfo($row['ID']);
    }

    else{
        returnWithError("Record not found");
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