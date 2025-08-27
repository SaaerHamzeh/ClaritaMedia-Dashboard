<?php
include("header.php");
  if (is_uploaded_file($_FILES['my-file']['tmp_name']) && $_FILES['my-file']['error']==0)
  {
    $path = '/var/www/html/phpforkids.com/uploads/' . $_FILES['my-file']['name'];
    if (!file_exists($path))
	{
		if (move_uploaded_file($_FILES['my-file']['tmp_name'], $path)) 
		  {
			echo "The file was uploaded successfully.";
		  }
		else
		  {
			echo "The file was not uploaded successfully.";
          }
    }
	else
	{
      echo "File already exists. Please upload another file.";
    }
  }
  else
  {
    echo "The file was not uploaded successfully.";
    echo "(Error Code:" . $_FILES['my-file']['error'] . ")";
  }
?>
        <div class="contact-us-container">
        	<div class="container">
	            <div class="row">		
					<div class="col-sm-10 col-sm-offset-1">
						<div class="service col-sm-10 col-sm-offset-1 wow fadeInLeft animated animated">
							<form  action="<?php echo $_SERVER["PHP_SELF"]?>" method="post" enctype="multipart/form-data">
								<div class="col-sm-4 form-group">
									<label for="contact-message">Work Image</label>
									<input type="file" name="WorImg" class="contact-subject form-control">
								</div>																																
							<center><input type="submit" name="Action" value="Add" class="btn-primary"></center>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
