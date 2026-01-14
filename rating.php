<?php

include "config.php";

$userid = 1;

// Validate POST data
if (!isset($_POST['postid']) || !isset($_POST['rating'])) {
    echo json_encode(["averagerating" => "0"]);
    exit;
}

$postid = intval($_POST['postid']);
$rating = intval($_POST['rating']);

// Check if user already rated this post
$query = "SELECT COUNT(*) AS cpost FROM p_rating 
          WHERE postid = $postid AND userid = $userid";

$result = $conn->query($query);
$row = $result->fetch_assoc();
$count = $row['cpost'];

// Insert or update rating
if ($count == 0) {
    $insertquery = "INSERT INTO p_rating (userid, postid, rating) 
                    VALUES ($userid, $postid, $rating)";
    $conn->query($insertquery);

} else {
    $updatequery = "UPDATE p_rating 
                    SET rating = $rating 
                    WHERE userid = $userid AND postid = $postid";
    $conn->query($updatequery);
}

// Get updated average rating
$query = "SELECT ROUND(AVG(rating), 1) AS averagerating 
          FROM p_rating WHERE postid = $postid";

$result = $conn->query($query);
$fetchaverage = $result->fetch_assoc();
$averagerating = $fetchaverage['averagerating'];

$return_arr = array("averagerating" => $averagerating);

// Return JSON
echo json_encode($return_arr);

?>