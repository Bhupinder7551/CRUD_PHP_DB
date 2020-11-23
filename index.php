<?php 
session_start();
    // initialize errors variable


	// connect to database
	$db = mysqli_connect("localhost", "root", "", "todo");
	$errors = "";
	$task = "";
	$name="";
	$id = 0;

	$update = false;

	// insert a quote if submit button is clicked
	if (isset($_POST['submit'])) {
		if (empty($_POST['task'])) {
			$errors = "You must fill in the task";
		}else{
			$name = $_FILES['file_']['name'];
        $target_dir = "upload/";
		$target_file = $target_dir . basename($_FILES["file_"]["name"]);
		
		$task = $_POST['task'];
		  // Select file type
		  $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

		  // Valid file extensions
		  $extensions_arr = array("jpg","jpeg","png","gif");

    if( in_array($imageFileType,$extensions_arr) ){
		  // Convert to base64 
		  $image_base64 = base64_encode(file_get_contents($_FILES['file_']['tmp_name']) );
		  $image = 'data:image/'.$imageFileType.';base64,'.$image_base64;

			
			$sql = "INSERT INTO tasks (task,name,image) VALUES ('$task','$name','$image')";
			mysqli_query($db, $sql);

			    // Upload file
				move_uploaded_file($_FILES['file_']['tmp_name'],'upload/'.$name);

			header('location: index.php');
		}else{
			print_r("not working");
		}
	}
}	
	if (isset($_GET['del_task'])) {
		$id = $_GET['del_task'];	
		mysqli_query($db, "DELETE FROM tasks WHERE id=".$id);
		header('location: index.php');
	}	
	
// if (isset($_POST['update'])) {
// 	$id = $_POST['id'];

// 	$name = $_FILES['file_']['name'];
//         $target_dir = "upload/";
// 		$target_file = $target_dir . basename($_FILES["file_"]["name"]);
		
// 		$task = $_POST['task'];
// 		  // Select file type
// 		  $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// 		  // Valid file extensions
// 		  $extensions_arr = array("jpg","jpeg","png","gif");

//     if( in_array($imageFileType,$extensions_arr) ){
// 		  // Convert to base64 
// 		  $image_base64 = base64_encode(file_get_contents($_FILES['file_']['tmp_name']) );
// 		  $image = 'data:image/'.$imageFileType.';base64,'.$image_base64;
// //print_r($task);
// 	mysqli_query($db, "UPDATE tasks SET task='$task',name='$name',image='$image' WHERE id=$id");
//  // Upload file
//  move_uploaded_file($_FILES['file_']['tmp_name'],'upload/'.$name);

// 	header('location: index.php');
// 	}
// }
// 	if (isset($_GET['edit_task'])) {
// 		$id = $_GET['edit_task'];	
// 		$update = true;
// 		$record = mysqli_query($db, "SELECT * FROM tasks WHERE id=".$id);
	
// 		if ($record) {
// 			$n = mysqli_fetch_array($record);
// 			$task = $n['task'];
// 			$name=$n['name'];
// 		} else {
// 			// UPDATE failed
// 			echo mysqli_error($db);
// 			db_disconnect($db);
// 			exit;
// 		  }
// 	}
	
if (isset($_POST['update'])) {
	$id = $_POST['id'];
	$task = $_POST['task'];
	$name = $_FILES["file_"]["name"];
	$name_temp = $_FILES["file_"]['tmp_name'];


	if($name_temp != "")
	{
		move_uploaded_file($_FILES['file_']['tmp_name'],'upload/'.$name);
		mysqli_query($db, "UPDATE tasks SET task='$task',name='$name',image='$image' WHERE id=$id");
		}else
	{
		mysqli_query($db, "UPDATE tasks SET task='$task',name='$name' WHERE id=$id");
	}
	

	header('location: index.php');
	}

	if (isset($_GET['edit_task'])) {
		$id = $_GET['edit_task'];	
		$update = true;
		$record = mysqli_query($db, "SELECT * FROM tasks WHERE id=".$id);
	
		if ($record) {
			$n = mysqli_fetch_array($record);
			$task = $n['task'];
			$name=$n['name'];
		} else {
			// UPDATE failed
			echo mysqli_error($db);
			db_disconnect($db);
			exit;
		  }
	}
	

	?>
<!DOCTYPE html>
<html>
<head>
	<title>ToDo List Application PHP and MySQL</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div class="heading">
		<h2 style="font-style: 'Hervetica';">ToDo List Application PHP and MySQL database</h2>
	</div>
	<form method="POST" action="index.php" class="input_form" enctype='multipart/form-data'>

	<?php if (isset($errors)) { ?>
	<p><?php echo $errors; ?></p>
<?php } ?>

<input type="hidden" name="id" value="<?php echo $id; ?>">

<input type="text" name="task" class="task_input" value="<?php echo $task; ?>">
		<?php if ($update == true): ?>
			<img src="upload/<?php echo $name; ?>" width="50px" height="50px"  >
			<input type="file" name="file_" class="task_input" value="<?php echo $name; ?>" >
<?php else: ?>
		<input type="file" name="file_" class="task_input" >
<?php endif ?>
		<?php if ($update == true): ?>

			<button type="submit" name="update" class="add_btn">Update</button>
			<?php else: ?>
		<button type="submit" name="submit" id="add_btn" class="add_btn">Add Task</button>
		<?php endif ?>

	</form>
	
	

	<table>
	<thead>
		<tr>
			<th>N</th>
			<th>Tasks</th>	
		    <th>Images</th>
			<th style="width: 60px;">Action</th>
		</tr>
	</thead>

	<tbody>
		<?php 
		// select all tasks if page is visited or refreshed
		$tasks = mysqli_query($db, "SELECT * FROM tasks");

		$i = 1; while ($row = mysqli_fetch_array($tasks)) { ?>
			<tr>
				<td> <?php echo $i; ?> </td>
				<td class="task"> <?php echo $row['task']; ?> </td>
				<td class="task"><img src="upload/<?php echo $row['name']; ?>" width="50px" height="50px"  > </td>
				<td class="delete"> 
					<a href="index.php?edit_task=<?php echo $row['id'] ?>">edit</a> 
				</td>	<td class="delete"> 
					<a href="index.php?del_task=<?php echo $row['id'] ?>">delete</a> 
				</td>
			</tr>
		<?php $i++; } ?>	
	</tbody>
</table>
</body>
</html>