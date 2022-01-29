<?php

  $inData = getRequestInfo();
  $recordID = $inData["recordID"];

  // establishes connection with mysqli, and errors out if the connection fails
  $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
  if ($conn->connect_error) 
  {
    returnWithError($conn->connect_error);
  } 
  // else if the sql session was opened without error
  else
  {
    // grabs the information associated with the given RecordID
    $stmt = $conn->prepare("SELECT * FROM Records WHERE ID=?");
    $stmt->bind_param("s", $recordID);
    $stmt->execute();
    $result = $stmt->get_result();

    // if there is a contact with the given RecordID, then delete it
    if ($row = $result->fetch_assoc())
    {
      $stmt2 =  $conn->prepare("DELETE FROM Records WHERE ID=?");
      $stmt2->bind_param("s", $recordID);
      $stmt2->execute();
      returnWithError("");
      $stmt2->close();
    }
    // if there isn't a contact with the given RecordID, then error out
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
?>