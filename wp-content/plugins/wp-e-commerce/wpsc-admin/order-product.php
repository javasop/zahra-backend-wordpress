<?php
require_once("database/connect.php");
if($_POST['id']){
$id=trim($_POST['id']);
$email=trim($_POST['mail']);
$name=trim($_POST['name']);

$getorderDetails=$db->Execute("select","select * from order_product where order_id='$id'");

$getOrdereddetail=$db->Execute("select","select * from  wp_order where ID='$id'");

}

?>
<h2>Ordered Product Detail </h2>
<h3>Order By :<?php echo $name; ?></h3> 
<h3>Order Date :<?php echo $getOrdereddetail[0]['date']; ?></h3> 
<table class="coupon-list widefat" cellspacing="0">
<thead>
<tr>
<th id="coupon_code" class="manage-column column-coupon_code" style="" scope="col">Product ID</th>
<th id="discount" class="manage-column column-discount" style="" scope="col">Product Name</th>
<th id="start" class="manage-column column-start" style="" scope="col">Product Price</th>
<th id="expiry" class="manage-column column-expiry" style="" scope="col">Product Quanitity</th>
<th id="active" class="manage-column column-active" style="" scope="col">Ordered Email</th>
<th id="apply_on_prods" class="manage-column column-apply_on_prods" style="" scope="col">Total Price</th>
<th id="edit" class="manage-column column-edit" style="" scope="col"></th>
</tr>
</thead>

<tbody>


<tr class='alt'>


<td style='height:39px;cursor:pointer;'> <?php echo $getorderDetails[0]['product_id'];?></td>
<td><?php echo $getorderDetails[0]['product_name'];?></td>
<td><?php echo $getorderDetails[0]['price'];?></td>
<td><?php echo $getorderDetails[0]['quantity'];?></td>
<td><?php echo $email;?></td>
<td><?php echo intval($getorderDetails[0]['quantity'])*intval($getorderDetails[0]['price']);?></td>
<td></td>

</tr>

</tbody>
</table>
<a href="javascript:closeblock('orderDetail')">Close</a>