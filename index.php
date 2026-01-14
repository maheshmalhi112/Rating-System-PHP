<?php
include "config.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Star Rating System</title>

    <link href="style.css" type="text/css" rel="stylesheet"/>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <!-- Font Awesome (required for stars) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Bar Rating Plugin -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-bar-rating/1.2.2/themes/fontawesome-stars.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-bar-rating/1.2.2/jquery.barrating.min.js"></script>

    <script type="text/javascript">
        $(function () {

            $('.rating').barrating({
                theme: 'fontawesome-stars',
                onSelect: function (value, text, event) {

                    var el = this;
                    var el_id = el.$elem.data('id');

                    if (typeof(event) !== 'undefined') {

                        var split_id = el_id.split("_");
                        var postid = split_id[1];

                        $.ajax({
                            url: 'rating.php',
                            type: 'post',
                            data: { postid: postid, rating: value },
                            dataType: 'json',
                            success: function (data) {
                                $('#avgrating_' + postid).text(data.averagerating);
                            }
                        });
                    }
                }
            });

        });
    </script>

</head>
<body>

<?php
$userid = 1;

$query = "SELECT * FROM post";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {

    $postid = $row['id'];
    $title = $row['title'];
    $content = $row['content'];
    $link = $row['link'];

    // Fetch user rating
    $query = "SELECT * FROM p_rating WHERE postid='$postid' AND userid='$userid'";
    $resul = $conn->query($query);
    $fetchrating = $resul->fetch_assoc();
    $rating = $fetchrating ? $fetchrating['rating'] : 0;

    // Fetch average rating
    $query = "SELECT ROUND(AVG(rating),1) AS averagerating FROM p_rating WHERE postid='$postid'";
    $avgresult = $conn->query($query);
    $fetchaverage = $avgresult->fetch_assoc();
    $averagerating = $fetchaverage['averagerating'];

    if ($averagerating <= 0) {
        $averagerating = "No rating yet";
    }
    ?>

    <div class="post">
        <h1><a href="<?php echo $link; ?>" class="-link" target="_blank"><?php echo $title; ?></a></h1>

        <div class="post-text">
            <?php echo $content; ?>
        </div>

        <div class="post-action">

            <select class="rating" id="rating_<?php echo $postid; ?>" data-id="rating_<?php echo $postid; ?>">
                <option value=""></option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>

            <div style="clear:both"></div>

            Average Rating:
            <span id="avgrating_<?php echo $postid; ?>">
                <?php echo $averagerating; ?>
            </span>

            <script type="text/javascript">
                setTimeout(function() {
                    $('#rating_<?php echo $postid; ?>').barrating('set', <?php echo $rating; ?>);
                }, 200);
            </script>

        </div>
    </div>

    <?php
}
?>

</body>
</html>