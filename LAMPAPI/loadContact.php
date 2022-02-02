<?php
  /*
    UserID is cached, and the other contact info is scraped from the screen. This
    loads the RecordID to be pinged in deleteContact and updateContact
  */

  // scraped info
  $inData = getRequestInfo();
  $firstName = $inData["firstName"];
  $lastName = $inData["lastName"];
  $email = $inData["email"];
  $phone = $inData["phone"];
  // cached data
  $userID = $inData["userID"]; // logged in user id, NOT record id

  // establishes connection with mysqli, and errors out if the connection fails
  $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
  if ($conn->connect_error)
  {
    returnWithError($conn->connect_error);
  }
  // else if the sql session was opened without error
  else
  {
    // Grabs the RecordID associated with the name, email, phone, and UserID
    $stmt = $conn->prepare("SELECT * FROM Records WHERE FirstName=? AND LastName=? AND Email=? AND Phone=? AND UserID=?");
    $stmt->bind_param("sssss", $firstName, $lastName, $email, $phone, $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    // if the RecordID for the contact exists, return it
    if ($row = $result->fetch_assoc())
    {
      returnWithInfo($row['ID'], $row["FirstName"], $row["LastName"], $row["Email"], $row["Phone"]);
    }
    // if the RecordID for the contact doesn't exist, error out
    else
    {
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

  function returnWithInfo($recordID, $firstName, $lastName, $email, $phone)
  {
    $retValue = '{"recordID": "' . $recordID . '", "firstName": "' . $firstName . '", "lastName": "' . $lastName . '", "email":"' . $email . '", "phone":"' . $phone . '","error":""}';
    sendResultInfoAsJson($retValue);
  }
?>