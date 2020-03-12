<?php

if(isset($_POST['caseSubmit'])){
  $caseNum = $_POST['caseNum'];
  $caseDate = $_POST['caseDate'];
  $caseState = $_POST['caseState'];
  $caseLink = $_POST['caseLink'];

  $errorEmpty = false;


  if(empty($caseNum) || empty($caseDate) || empty($caseState) ||empty($caseLink)){
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
    $("#caseDate, #caseNum, #caseState, #caseLnk").val("");
  }//empty out form if no error

</script>


<!-- https://www.youtube.com/watch?v=L7Sn-f36TGM -->
