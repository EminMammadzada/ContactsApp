<?php

  /*
    User ID is given from a variable, and the first name, last name, email, and
    phone number are given from user input. This takes the user input and searches for a contact
    with the specified User ID (if the user has this contact)
  */

  // scraped info
  $inData = getRequestInfo();
  $arrayOfInputs = $inData["inputs"];

  // session info
  //TODO: change all "id" to 'userID'
  $userID = $inData["userId"]; // logged in user id, NOT record id

  $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
  if ($conn->connect_error)
  {
    returnWithError( $conn->connect_error );
  }
  // else if the sql session was opened without error
  else
  {
    // One search bar need input seperated by spaces into an array, arrayOfInputs
    // length of array of inputs array is stored in $numInputs
    $numInputs = sizeof($arrayOfInputs);


    // grabs
    $userAttemptedSQLInjection = false;
    $index = 0;
    while ($numInputs > 0)
    {
      echo $index;
      echo $arrayOfInputs["$index"] . "\n";
      echo "entering the while loop\n";
      // if the user is not attempting an SQL injection at this index, continue to concatenate the massive SQL command
      if (!str_contains($arrayOfInputs[$index], ';')
             && !str_contains($arrayOfInputs[$index], ';')
             && !str_contains($arrayOfInputs[$index], ';')
             && !str_contains($arrayOfInputs[$index], ';'))
      {
        echo "no SQL injection here lol\n";
        // every index after the first should INTERSECT with the following concatenated commands
        if ($index > 0)
        {
          echo "adding intersect...\n";
          $command .= " INTERSECT ";
        }
        echo "before the load\n";
        echo $arrayOfInputs[$index] . "\n";
        $command .= "SELECT * FROM Records WHERE UserID = " . $userID . " AND FirstName LIKE '%" . $arrayOfInputs[$index] . "%' OR LastName LIKE '%" . $arrayOfInputs[$index] . "%' OR Phone LIKE '%" . $arrayOfInputs[$index] . "%' OR Email LIKE '%" . $arrayOfInputs[$index] . "%'";
        echo "post-load\n";
        $index++;
        $numInputs--;
        echo "about to loop again\n\n";
      }
      else
      {
        echo "ATTEMPTED INJECTION WEEWOOWEEWOO";
        $userAttemptedSQLInjection = true;
      }
    }

    // if the user did not attempt an SQL injection, execute the command
    if (!$userAttemptedSQLInjection)
    {
      $command .= ";";
      echo "ok you didn't inject you don't suck\n";
      echo $command . "\n";
      $stmt = $conn->prepare($command);
      $stmt->execute();
      $result = $stmt->get_result();
      echo $result . "\n";
  
      // if there is such contact, then delete it
      if ($row = $result->fetch_row())
      {
        sendResultInfoAsJson($result);
      }
      // if contact does not exist, then error out
      else
      {
        returnWithError("No such contact exists");
      }
  
      $stmt->close();
    }
    else 
    {
      youSuck();
    }
    $conn->close();

  }

  function getRequestInfo()
  {
    return json_decode(file_get_contents('php://input'), true);
  }
  function youSuck()
  {
    echo "You Suck.";
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

  /*

  {
    "id": 15,
    "inputs":
      [
        "daniel",
        "cosentino",
        "",
        "daniel.cosentinofl@gmail.com"
      ],
    "error": ""
  }

  {
    "id": 15,
    "results":
    [
      {
        "firstName": "daniel",
        "lastName": "cosentino",
        "phone": "4075909460",
        "email": "daniel.cosentinofl@gmail.com"
      },
      {
        "firstName": "Matthew",
        "lastName": "",
        "phone": "",
        "email": ""
      },
    ],

    "error": ""
  }


  {
    "id": 15,
    "results":
    [
      {
        "firstName": "daniel",
        "lastName": "cosentino",
        "phone": "4075909460",
        "email": "daniel.cosentinofl@gmail.com"
      },
      {
        "firstName": "Matthew",
        "lastName": "",
        "phone": "",
        "email": ""
      },
    ],

    "error": ""
  }

  Gerber 8881720129


  // One search bar need input seperated by spaces into an array, arrayOfInputs
  // length of array of inputs array is stored in $numInputs


  $command = "SELECT * FROM Records WHERE FirstName LIKE "%" . $arrayOfInputs[0] . "%" OR LastName LIKE "%" . $arrayOfInputs[0] . "%" OR Phone LIKE "%" . $arrayOfInputs[0] . "%" OR Email LIKE "%" . $arrayOfInputs[0] . "%")""
  $numInputs--;
  $index = 1;
  while ($numInputs > 0)
  {
    $command .= "INTERSECT SELECT * FROM Records WHERE " . arrayOfInputs[$index] . " IN (FirstName, LastName, Phone, Email)"
    $index++;
  }
  command.execute();
  */
?>