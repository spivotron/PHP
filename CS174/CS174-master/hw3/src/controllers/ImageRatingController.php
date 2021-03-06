<?php

namespace spivotron\hw3\controllers;
require_once "Controller.php";
require_once "src/configs/Config.php";
use spivotron\hw3\config as config;

class ImageRatingController extends Controller {

  function processRequest() {

      $data = [];
      session_start();
      // code for gathering photos from db
      ini_set('display_errors',1);
      error_reporting(E_ALL);

      $conn = new \mysqli(config\Config::HOST, config\Config::USER, config\Config::PWD, config\Config::DB);
      // Check connection
      if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
      } 
      if($conn) {
      	// get the 3 most recently uploaded images
        $retrieve = "SELECT * FROM Images ORDER BY 'upload_time' DESC LIMIT 3";
        if (!$conn->query($retrieve) === TRUE) {
            echo "Error: " . $conn->error . "<br/>";
        } else {
        	$tmpCount = 0;
	        $data['retrieve'] = $conn->query($retrieve);
	        if ($data['retrieve']->num_rows > 0) {
	          // output data of each row            	
	          while($row = $data['retrieve']->fetch_assoc()) {   
	          			
	        		$title = $row["title"];
	        		$user = $row["user_id"];
	        		$caption = $row["caption"];

	        		$newData[$tmpCount]['USER'] = $user;
	        		$newData[$tmpCount]['TITLE'] = $title;
	        		$newData[$tmpCount]['CAPTION'] = $caption;
	        		$newData[$tmpCount]['RATING'] = $row['rating'];

	        		$data['NUMROWS'] = $data['retrieve']->num_rows;
	        		$data['ALLIMAGES'] = $newData;

	        		$tmpCount++;        
	          }              
	        } 
        }
        $popular = "SELECT * FROM Images ORDER BY 'upload_time' DESC LIMIT 10";
        if (!$conn->query($popular) === TRUE) {
            echo "Error: " . $conn->error . "<br/>";
        } else {
        	
	        $data['popular'] = $conn->query($popular);
	        if ($data['popular']->num_rows > 0) {
	          // output data of each row            	
	          while($row = $data['popular']->fetch_assoc()) {   
	          			
	        		$title = $row["title"];
	        		$user = $row["user_id"];
	        		$caption = $row["caption"];

	        		$newPopularData[$tmpCount]['USER'] = $user;
	        		$newPopularData[$tmpCount]['TITLE'] = $title;
	        		$newPopularData[$tmpCount]['CAPTION'] = $caption;
	        		

	        		$data['NUMROWS_POPULAR'] = $data['retrieve']->num_rows;
	        		$data['ALLIMAGES_POPULAR'] = $newData;

	        		$tmpCount++;        
	          }              
	        } 
        }

      
			if(isset($_REQUEST['rating'])){
				$currentRating = $_REQUEST['rating'];	
				$user = $_SESSION['username'];

				// if there are previous ratings in the DB then I need to add the current rating to the average
				if (isset($currentRating)) {
					$runningTotal = 0;
					$numberOfRatings = 0;

					$getImage = "SELECT rating FROM Image WHERE title = '$title'";
					$getNumberOfRatings = "SELECT * FROM ratings WHERE image_title = '$title'";
					if($result=mysqli_query($conn, $getNumberOfRatings)) {
						
						// loop through each rating
						if($result->num_rows > 0) {

						while($getRatings = mysqli_fetch_array($result)) {
							$rating = $getRatings['rating'];
							$numberOfRatings = $getRatings['ratings'];
							$runningTotal = $runningTotal + $rating;
						}

						$runningTotal = $runningTotal + $currentRating;
						$newRating = round($runningTotal / $numberOfRatings);

						if (isset($_REQUEST['imageToRate'])) {
							$titleOfImageToUpdate = $_REQUEST['imageToRate'];

							$sql = "UPDATE Images SET rating = '$newRating' WHERE title = '$titleOfImageToUpdate'";
							if (!$conn->query($sql) === TRUE) {
							    echo "Error: " . $conn->error . "<br/>";
							} else {
							  $updateImages = $conn->query($sql);
							}
							// update the ratings table
							$newCount = $numberOfRatings + 1;
							$ratingSQL = "UPDATE ratings SET rating = '$currentRating', ratings = '$newCount', rated_by = '$user' WHERE image_title = '$title'";
							if (!$conn->query($ratingSQL) === TRUE) {
							    echo "Error: " . $conn->error . "<br/>";
							} else {
							  $updateRatings = $conn->query($ratingSQL);
							}
						}
					} else {
						// update the ratings table
						$newCount = $numberOfRatings + 1;
						$ratingSQL = "INSERT INTO ratings (image_title, rating, ratings, rated_by) VALUES ('$title', '$currentRating', '$newCount', '$user')";
						if (!$conn->query($ratingSQL) === TRUE) {
						    echo "Error: " . $conn->error . "<br/>";
						} else {
						  $updateRatings = $conn->query($ratingSQL);
						}
					}
				}					
				}
			}
		}

			$conn->close();
      $data['UPLOADED_FILE'] = $this->sanitize("imageFile", "file");
      $data['UPLOADED_FILE_VALID'] = $this->validate("imageFile", "file");

      $this->view("imageRating")->render($data);
  }
}
