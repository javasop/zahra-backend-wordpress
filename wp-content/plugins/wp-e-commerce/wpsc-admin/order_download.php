<?php
    header("Content-type: application/x-msdownload");
    header("Content-Disposition: attachment; filename=extraction.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    require_once("database/connect.php");
    echo "Project ID,Product Name,Quantity,First Name,Last,Email,Phone,City,Region,District,Order Date\r\n"; //header
    
    
   // while($row = mysql_fetch_array($result)){
    //echo "\"$row[id]\",\"$row[first]\",\"$row[middle]\",\"$row[last]\",\"$row[email]\",\"$row[email]\r\n"; //data
   // }
    $getTotalOrder=$db->rowcount("select * from wp_order");
    $getTotalOrderData=$db->Execute("select","select * from wp_order");
    
    for($i=0;$i<$getTotalOrder;$i++) {
    $id=$getTotalOrderData[$i]['ID'];
    $getProductDetails=$db->Execute("select","select * from order_product where order_id='$id'");
    
   echo $getTotalOrderData[$i]['ID'].",".$getProductDetails[0]['product_name'].",".$getProductDetails[0]['quantity'].",".$getTotalOrderData[$i]['first_name'].",".$getTotalOrderData[$i]['last_name'].",".$getTotalOrderData[$i]['email'].",".$getTotalOrderData[$i]['phone'].",".$getTotalOrderData[$i]['city'].",".$getTotalOrderData[$i]['region'].",".$getTotalOrderData[$i]['district'].",".$getTotalOrderData[$i]['date']."\r\n"; 
    
    }
    
    
    
    
    ?>
