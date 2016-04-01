<?php
/**
 *COPYRIGHT (C) 2016 Tyler Jones. All Rights Reserved.
 * ImageRatingController.php is the controller for the main Image Rating System screen
 * Solves CS174 Hw3
 * @author Tyler Jones
*/
namespace soloRider\hw3\controllers;
require_once "Controller.php";
require_once "auth.php";

class ImageRatingController extends Controller {


//PASTED IN AS EXAMPLE NEED TO EDIT

    /**
     * Used to handle form data coming from EmailView.
     * Should sanitize that data and check if the email within it was
     * valid.
     */
    function processRequest() {
        $data = [];
        // Your code here
        //$data['PREVIOUS_EMAIL'] = $this->sanitize("email", "email");
        //$data['PREVIOUS_EMAIL_VALID'] = $this->validate($data['PREVIOUS_EMAIL'], "email");

        $data['UPLOADED_FILE'] = $this->sanitize("imageFile", "file");
        $data['UPLOADED_FILE_VALID'] = $this->validate("imageFile", "file");

        $this->view("imageRating")->render($data);
    }
}
