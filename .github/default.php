<?php session_start(); ?> <!-- 


|\            /|    __________     |-------\    |       |--------\      |----------
|  \        /  |    |              |        |   |       |         |     |
|    \    /    |    |              |       /    |       |         /     |--------
|      \/      |    |---------     |-----/      |       |-------/       |
|              |    |              |            |       |               |
|              |    |_________     |            |       |               |----------     .NET


© COPYRIGHT. ALL RIGHTS RESERVED. 
PRODUCTION OF JOSHY CO. LIMITED



-->





















<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>MePipe</title>
    <link rel="icon" href="image/favicon.ico?v=1.0" type="image/x-icon">
    <link href="main.css?v=1.99001" rel="stylesheet" type="text/css" />
</head>
<body>
    
    <div class="bar">
        <?php if ($_SESSION['loggedin']){
            ?>
                <div class="buttonz">
                    <a class="buttonx" href="homepage.php"> MY HOMEPAGE </a>
                </div>
                <div class="buttonz">
                    <a class="buttonx" href="PayMate.php"> MY PAYMATE </a>
                </div>
                <div class="buttonz">
                    <a class="buttonx" href="logout.php"> LOG OUT </a>
                </div>
                <div class="buttonz">
                    <a style='font-size: 18px; font-family: "Gill Sans", sans-serif;' href="upgrade.php"> UPGRADE YOUR ACCOUNT </a>
                </div>
            <?php 
        }
        ?>
        <div class= "buttonz">
            <a class="buttonx" href="upload.php"> UPLOAD A VIDEO!</a>
        </div>
        <div class="buttonz">
            <a class="buttonx" href="mailing.html"> SIGN UP </a> <br>
        </div>
        <div class="buttonz">
            <a class="buttonx" href="updatelog.html"> MEPIPE'S UPDATES </a>
        </div>
        <?php
        if ($_SESSION['loggedin'] == false){
            ?>
            <div class="buttonz">
                    <a class="buttonx" href="Login.html"> LOG IN</a> <br>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="switchbar" id="switchbar">
        <form action="Instakilogram.php">
            <button type="submit" class="barbuttons">Instakilogram</button>
        </form>
        <form action="MeQuery.php">
            <button type="submit" class="barbuttons">MeQuery</button>
        </form>
    </div>
    <img class="switch" onclick="DisplayOptions();" src="image/favicon (2).ico">
    <div class="video-container">
    <?php
        // Define the directory where videos are stored
        $video_num = 0;
        $directory = 'uploads/';
        $servername = "localhost";
        $username = "ux3wk7gtwoega";
        $password = "81lzz2nlt2lf";
        $dbname = "dbgestigzgxydt";
        // Create a connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        if ($_SERVER["REQUEST_METHOD"] == "GET"){
            $statement = "SELECT v.id, v.creator_id, v.video_name, v.file_path, v.likes, u.username 
                FROM videos v 
                JOIN users u ON v.creator_id = u.id 
                WHERE LOWER(v.video_name) LIKE ?";
            $searchTerm = '%' . strtolower($_GET['search']) . '%';
            $stmt = $conn->prepare($statement);
            // Bind the parameter (s = string)
            $stmt->bind_param("s", $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }else{
            $statement = "SELECT v.id, v.creator_id, v.video_name , v.file_path, v.likes, u.username
                FROM videos v JOIN users u ON v.creator_id = u.id";
            $result = $conn->query($statement);
            $rows = $result->fetch_all(MYSQLI_ASSOC);
        }
        
        //$stmt = $conn->prepare($statement);
        //$stmt->execute();
        
        /*if ($_SESSION['loggedin']){
            $statement = "SELECT video_name FROM `user_likes` WHERE username = '{$_SESSION['username']}' AND liked = 1;";
            $likedResult = $conn->query($statement);
            $likedData = $likedResult->fetch_assoc();
        }*/
        
        $displayedVidIds = [];
        foreach ($rows as $row){
            array_push($displayedVidIds, $row['id']);
        }
        $displayedVidIds_String = implode(',', $displayedVidIds);
        if ($displayedVidIds_String != null){
            $statement2 = "SELECT video_id FROM user_likes WHERE video_id IN (".$displayedVidIds_String.") AND username = '".$_SESSION['username']."';";
            $result2 = $conn->query($statement2);
            $likedResultMulti = $result2->fetch_all(MYSQLI_ASSOC);
            $likedResult = array_column($likedResultMulti, 'video_id');
        }
        $conn->close();
        usort($rows, function($a, $b) {
            return $b['likes'] <=> $a['likes'];
        });

        foreach ($rows as $row){
            $file = $row['file_path'];
            $file_info = pathinfo($file);
            $extension = $file_info['extension'];
            if($extension == "mp4"){
                $video_id = $row['id'];
                //print_r();
                if ($likedResult != null){
                    if (in_array($video_id, $likedResult)){
                        $liked_or_not = true;
                    }else{
                        $liked_or_not = false;
                    }
                }else{
                    $liked_or_not = false;
                }
                ?>


                <div class='vid'>
                    <video style="width:400px; height: 200px;"controls class="videos">
                    <source src="<?php echo $directory . $file  ?>" type="video/<?php echo $extension; ?>">
                    </video>
                    <p class="titleV"> <?php echo $row['video_name']; ?> </p>
                    <form action="profile.php">
                        <input type="hidden" name="profile" value="<?php echo $row['username']; ?>">
                        <input type="hidden" name="profileId" value="<?php echo $row['creator_id']; ?>">
                        <button class="profilelink" type="submit"> created by <?php echo $row['username'] ?></button> <br>
                    </form>
                    <div class="options">
                        
                        <form class="like" method="post" action="update_like.php">
                            <p class="likes">Likes: <span class="likesCount"><?php echo $row['likes']; ?></span> </p>
                            <input type="hidden" name="vidId" value="<?php echo $video_id ?>">
                            <input type="hidden" name="liked" value="<?php echo $liked_or_not ?>">
                            <button onclick="hidb()" type="submit" style="margin: 5px" class="likeButton"><?php 
                            if ($liked_or_not){
                                echo "unlike";
                            }else{
                                echo "like";
                            }
                            ?></button>
                        </form>
                        <form action="comment.php" method="get">
                            <input name = "vidid" type="hidden" value="<?php echo $file ?>">
                            <button type="submit" style="margin: 5px">comment!</button>
                        </form>
                    </div>
                </div>
                <?php 
            }        
        }
        
        ?>
    </div>
    <div class="headcontainer">
        <img src="image/favicon.ico?v=1.0">
        <h1 class="head">MePipe</h1>
        <div class="searchbar">
            <form action="default.php" method="get">
                <input class="search" type="search" placeholder="search something.." name="search">
                <button class="searchbutton" type="submit">search</button>
            </form>
        </div>
    </div>
    <div class="bottom">
        <p class="link" style="font-size: 15px"> Nexora co. © All rights reserved </p>
        <a class="link"href="https://sites.google.com/view/nexora-co/home">About us</a>
    </div>
    <script src="script.js"></script>
</body>
</html>
