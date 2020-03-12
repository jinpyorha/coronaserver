<?php

if(isset($_POST['news-submit'])){
  $news_date = $_POST['news-date'];
  $news_headline = $_POST['news-headline'];
  $news_link = $_POST['news-link'];

  $errorEmpty = false;


  if(empty($news_date) || empty($news_headline) || empty($news_link)){
      echo "<span>Fill in all fields</span>";
      $errorEmpty=true;
  }
    else{
    echo "<span> all success </span>";
  }
}
else{
  echo "there was an error";
}
?>

<script>
  // $("#mail-name, #mail-email, #mail-message, #mail-gender").removeClass("input-error");
  var errorEmpty = "<?php echo $errorEmpty; ?>";

  // if(errorEmpty == true) {
  //   $("#mail-name, #mail-email, #mail-message").addClass("input-error");
  // }
  // if(errorEmail == true){
  //   $("#mail-email").addClass("input-error");
  // }
  if(errorEmpty == false){
    $("#news-date, #news-headline, #news-link").val("");
  }//empty out form if no error

</script>


<!-- https://www.youtube.com/watch?v=L7Sn-f36TGM -->
