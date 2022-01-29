<?php
  /*
    Record ID and User ID are cached, and the first name, last name, email, and
    phone number are given from user input. This takes the user input and applies it to the contact
    with the specified Record ID.
  */

  $inData = getRequestInfo();
  // user input
  $firstName = $inData["firstName"];
  $lastName = $inData["lastName"];
  $email = $inData["email"];
  $phone = $inData["phone"];
  // cached data
  $recordID = $inData["recordID"];

  // establishes connection with mysqli, and errors out if the connection fails
  $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
  if ($conn->connect_error)
  {
    returnWithError($conn->connect_error);
  }
  else
  {
    $stmt = $conn->prepare("SELECT * FROM Records WHERE ID=?");
    $stmt->bind_param("s", $recordID);
    $stmt->execute();

    $result = $stmt->get_result();

    // if there is such contact, then update it with the user's input
    if ($row = $result->fetch_assoc())
    {
      // sets up the command for updating the contact with the specific recordID and userID
      $stmt2 =  $conn->prepare("UPDATE Records SET FirstName=?, LastName=?, Email=?, Phone=? WHERE ID=?");
      $stmt2->bind_param("sssss", $firstName, $lastName, $email, $phone, $recordID);
      $stmt2->execute();
      $result2 = $stmt2->get_result();
      returnWithError("");
      $stmt2->close();
    }
    // if the contact does not exist, error out
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