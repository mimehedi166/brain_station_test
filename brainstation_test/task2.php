<!DOCTYPE html>
<html>
<head>
    <title>TASK 2</title>
</head>
<body>
<?php

$final_result = [];
$mysqli = new mysqli("localhost","root","","brain_station_test");

/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}


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
// print_r($rows);exit;
foreach($rows as $key => $val)
{
  $sql1 = "SELECT (categoryId),name from catetory_relations JOIN category on category.id = catetory_relations.categoryId where ParentcategoryId IN (".$val['id'].")";
  $result1 = $mysqli->query($sql1);
  $rows1 = $result1->fetch_all(MYSQLI_ASSOC);
  $final_result[$key]['category_name'] = $val['name'];
  $final_result[$key]['total_items'] = $val['items'];

  foreach ($rows1 as $sec => $iVal) {
      # code...
    // echo $iVal['categoryId']."<br>";
    $sql2 = "(SELECT 
            COUNT(id) AS child_item_count
        FROM
            Item_category_relations
        WHERE
            categoryId IN (SELECT 
                    GROUP_CONCAT(categoryId)
                FROM
                    catetory_relations
                WHERE
                    ParentcategoryId = ".$iVal['categoryId']."))";
                    // echo $sql2;
    $result2 = $mysqli->query($sql2);
    $rows2 = $result2->fetch_all(MYSQLI_ASSOC);
    $final_result[$key][$sec]['child_name'] = $iVal['name'];
    $final_result[$key][$sec]['child_item_count'] = $rows2[0]['child_item_count'];

  }
  
}
echo "<pre>";

print_r($final_result);
echo "<br>
<b>Appology note : The result may not be accurate. need more time to debug and resolve it.</b>";
 ?>
</body>
</html>