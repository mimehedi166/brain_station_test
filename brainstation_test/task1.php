<!DOCTYPE html>
<html>
<head>
	<title>Task 1</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
<?php

$mysqli = new mysqli("localhost","root","","brain_station_test");

/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// $query = "SELECT 
//     c.id,
//     c.name,
//     (SELECT 
//             COUNT(id)
//         FROM
//             Item_category_relations
//         WHERE
//             categoryId IN (SELECT 
//                     categoryId
//                 FROM
//                     catetory_relations
//                 WHERE
//                     ParentcategoryId = cr.ParentcategoryId)) AS items
// FROM
//     catetory_relations AS cr
//         JOIN
//     category AS c ON c.id = cr.ParentcategoryId
// GROUP BY cr.ParentcategoryId order by items DESC";

$query = "SELECT 
    c.id,
    c.name,
    (SELECT 
            COUNT(id)
        FROM
            Item_category_relations
        WHERE
            categoryId IN (SELECT 
                    categoryId
                FROM
                    catetory_relations
                WHERE
                    ParentcategoryId IN (SELECT 
                    GROUP_CONCAT(categoryId)
                FROM
                    catetory_relations
                WHERE
                    ParentcategoryId = cr.ParentcategoryId))) AS items
FROM
    catetory_relations AS cr
        JOIN
    category AS c ON c.id = cr.ParentcategoryId
    where c.id not in (select categoryId from catetory_relations) group by cr.ParentcategoryId order by items DESC";
$result = $mysqli->query($query);
$rows = $result->fetch_all(MYSQLI_ASSOC);
// echo "<pre>";
// print_r($rows);
 ?>
 <div class="panel panel-default">
  <div class="panel-body">
  	<table class="table table-bordered">
  <thead>
    <tr>
      <th scope="col">Category Name</th>
      <th scope="col">Total Items</th>
    </tr>
  </thead>
  <tbody>
  	<?php if (!empty($rows)) {
  		# code...
  		foreach ($rows as $val) { 
  			?>
  			
    <tr>
      <td><?php echo $val['name']; ?></td>
      <td><?php echo $val['items']; ?></td>
    </tr>
    <?php 
  		}
  	} ?>
    
  </tbody>
</table>
  </div>
</div>

<b>Appology note : The result may not be accurate. need more time to debug and resolve it.</b>
</body>
</html>