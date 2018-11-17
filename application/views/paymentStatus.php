<?php
/**
 * Created by PhpStorm.
 * User: Rajeev
 * Date: 12-07-2018
 * Time: 06:05 PM
 */
?>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->




<div class="container">
    <div class="row text-center">
        <?php
        //$status ='success';
        if($status =='success'){
        ?>
        <div class="col-sm-6 col-sm-offset-3">
            <br><br> <h2 style="color:#0fad00">Success</h2>

            <p style="font-size:20px;color:#5C5C5C;">Payment success. Your order is complete</p>

            <!--p style="font-size:20px;color:#5C5C5C;">Orderid= $orderid   ;  Userid = <?php echo $userid;?> </p-->

            <br><br>
        </div>
            <?php
        }
        else{
        ?>
        <div class="col-sm-6 col-sm-offset-3">
            <br><br> <h2 style="color:#ff0000">Failed</h2>

            <p style="font-size:20px;color:#5C5C5C;">Payment failed. Status : <?php echo $status;?></p>

            <!--p style="font-size:20px;color:#5C5C5C;">Orderid= $orderid   ;  Userid = $userid </p-->

            <br><br>
            <br><br>
        </div>
            <?php
        }
        ?>

    </div>
</div>
