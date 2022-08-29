<?php
    function img_proc(){
    session_start();
    $location="upload_img.php";
    //upload img
    $target_dir = "images/uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check !== false) {
            // echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            // echo "File is not an image.";
            $uploadOk = 0;
        }
    
    }
    // Allow certain file formats
    // if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    // && $imageFileType != "gif" ) {
    //     echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    //     $uploadOk = 0;
    // }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $_SESSION['message']= "<div> Sorry, your file was not uploaded. It is not an image.</div>";
        header("Location: $location");
        return;
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            
            // $_SESSION['message']="<h4> The file ".basename( $_FILES["fileToUpload"]["name"])."  has been uploaded successfully</h4>";
            $_SESSION['message'].= "<h1>Here is your Mosaic Image</h1>"; 
        } else {
            $_SESSION['message']= "<div> Sorry, there was an error uploading your file.</div>";
        }
    }

    //star processing on img
    //check img type
    if($imageFileType == "jpg" || $imageFileType == "jpeg")
        $img = imagecreatefromjpeg($target_file);
    else if($imageFileType == "png")
        $img = imagecreatefrompng($target_file);
    else if($imageFileType == "gif")
        $img = imagecreatefromgif($target_file);
    else {$_SESSION['message'] .= "<div> Only JPG, JPEG, PNG & GIF files are allowed.</div>";
          header("Location: $location"); return;}

    $width = imagesx($img);
    $height = imagesy($img);
    $px2 = 0.5*28.346;//0.5 cm
    $ratio_wh = $width/$height;
    $w_cm = $width / 28.346;
    $h_cm = $height / 28.346;
    // echo $w_cm." ".$h_cm." ";
    $area_cm = $w_cm * $h_cm;
    $no_tile = $area_cm/0.25; //area_px
    // echo $no_tile;
    // $w_px = $px2*$no_tile;
    $w_new_px = sqrt($no_tile*$ratio_wh);
    $h_new_px = $no_tile/$w_new_px;
    
    $newImg = imagecreatetruecolor($width,$height);
    imagecopyresized($newImg,$img,0,0,0,0,round($w_new_px),round($h_new_px),$width,$height);

    # Create 100% version ... blow it back up to it's initial size:
    $newImg2 = imagecreatetruecolor($width,$height);
    imagecopyresized($newImg2,$newImg,0,0,0,0,$width,$height,round($w_new_px),round($h_new_px));

   
    //   save processed photo 
    $processed="images/processed/".basename( $_FILES["fileToUpload"]["name"]);
    imagepng($newImg2,$processed);
    //echo processed img
    $_SESSION['message'].= "<img src='".$processed."' style='max-width:100%; height:100%; margin:auto'>";

    //pick colors
    $_SESSION['message'].= "<h1> Here is your Palette </h1>";
    $prv=[];$prv_r = [];$prv_g = [];$prv_b = [];
    $close_color=[];
    $nearest_col = 35;
    for($x=0;$x<$width;round($x+=$px2))
    {
        for($y=0;$y<$height;round($y+=$px2))
        {   
            if($px2+$x<$width && $px2+$y<$height)
            { 
                $closed=false;//no closed color nw
                $rgb = imagecolorat($newImg2, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
              
                 foreach($prv as $c)
                 {
                    $prv_r = ($c >> 16) & 0xFF;
                    $prv_g = ($c >> 8) & 0xFF;
                    $prv_b = $c & 0xFF;
                    if($rgb == $c || (($r<=$prv_r+$nearest_col && $r>=$prv_r-$nearest_col ) && 
                    ($g<=$prv_g+$nearest_col && $g>=$prv_g-$nearest_col ) && 
                    ($b<=$prv_b+$nearest_col && $b>=$prv_b-$nearest_col ))){
                        // echo $c;
                        $closed = true;
                        break;
                    }
                 }
                // $close_color = imagecolorsforindex($newImg2, $close_color_index);
                if(!$closed){
                    array_push($prv,$rgb);
                    
                    $_SESSION['message'] .= '<div style="display:inline-block; margin:2px;background-color:rgb('. $r.','. $g.','. $b.'); width:100px;height:100px" ></div>';
                    
                }
            }
        }
    }

    # No need for a jpeg here :-)
    imagepng($newImg2,"images/processed/".basename( $_FILES["fileToUpload"]["name"]));
    imagedestroy($newImg2);
    header("Location: $location");
    // for($x=0;$x<$width;round($x+=$px2))
    // {
    //     for($y=0;$y<$height;round($y+=$px2))
    //     {
    //         $r=0;$g=0;$b=0;
    //         //each 2*2cm
    //         if($px2+$x<$width && $px2+$y<$height)
    //         { 
    //             for($i=$x;$i<round($px2+$x-1);$i++)
    //             {
    //                 for($j=$y;$j<round($px2+$y-1);$j++)
    //                 {
    //                     $rgb = imagecolorat($img, $i, $j);
    //                     $r += ($rgb >> 16) & 0xFF;
    //                     $g += ($rgb >> 8) & 0xFF;
    //                     $b += $rgb & 0xFF;
    //                     var_dump($r, $g, $b);
    //                 }
    //             }
    //         }
    //         $r/=($px2*$px2);
    //         $g/=($px2*$px2);
    //         $b/=($px2*$px2);
    //         $color = imagecolorallocate($newImg2,$r,$g,$b);
    //         imagesetpixel($newImg2,$x,$y,$color);
    //     }
    // }
    # Create 5% version of the original image:
    // $newImg = imagecreatetruecolor($width,$height);
    // imagecopyresampled($newImg,$img,0,0,0,0,round($w_new_px),round($h_new_px),$width,$height);

    // # Create 100% version ... blow it back up to it's initial size:
    // $newImg2 = imagecreatetruecolor($width,$height);
    // imagecopyresampled($newImg2,$newImg,0,0,0,0,$width,$height,round($w_new_px),round($h_new_px));

   }
    add_shortcode('mosaic_view','img_proc');
?>