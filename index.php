<?php 
session_start();
    // initialize errors variable


	// connect to database
	$db = mysqli_connect("localhost", "root", "", "todo");
	$errors = "";
	$task = "";
	$id = 0;

	$update = false;

	// insert a quote if submit button is clicked
	if (isset($_POST['submit'])) {
		if (empty($_POST['task'])) {
			$errors = "You must fill in the task";
		}else{
			$task = $_POST['task'];
			$sql = "INSERT INTO tasks (task) VALUES ('$task')";
			mysqli_query($db, $sql);
			header('location: index.php');
		}
	}	
	if (isset($_GET['del_task'])) {
		$id = $_GET['del_task'];
	
		mysqli_query($db, "DELETE FROM tasks WHERE id=".$id);
		header('location: index.php');
	}	
	
if (isset($_POST['update'])) {
	$id = $_POST['id'];
	$task = $_POST['task'];

	mysqli_query($db, "UPDATE tasks SET task='$task' WHERE id=$id");

	header('location: index.php');
}
	if (isset($_GET['edit_task'])) {
		$id = $_GET['edit_task'];
		$update = true;
		$record = mysqli_query($db, "SELECT * FROM tasks WHERE id=".$id);
	
		if ($record) {
			$n = mysqli_fetch_array($record);
			$task = $n['task'];
			
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
	<form method="POST" action="index.php" class="input_form">

	<?php if (isset($errors)) { ?>
	<p><?php echo $errors; ?></p>
<?php } ?>

<input type="hidden" name="id" value="<?php echo $id; ?>">

		<input type="text" name="task" class="task_input" value="<?php echo $task; ?>">
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