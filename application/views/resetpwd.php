<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>MR Jobs</title>
    <link rel="shortcut icon" href="<?php echo base_url() ?>assets/favicon.ico" type="image/x-icon">
    <link rel="icon" href="<?php echo base_url() ?>assets/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i"
          rel="stylesheet">
    <!-- Bootstrap -->
    <link href="<?php echo base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url() ?>assets/css/dataTables.bootstrap.css" rel="stylesheet">
    <link href="<?php echo base_url() ?>assets/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/jquery-ui.css">
    <link href="<?php echo base_url() ?>assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.css" rel="stylesheet"/>
    <!-- SET GLOBAL BASE URL -->
    <script>var BASEURL = '<?php echo base_url() ?>index.php/';</script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
<section class="centerBlock">
    <div class="container" id="forgot_body">
        <!--  <div class="row">--->
        <div class="login-box">
            <div id="login-logo" style="text-align: center">
                <img src="<?php echo base_url() ?>assets/img/fetch_logo.png" alt="MR Jobs"
                     style="width:95px;height: 95px;object-fit: contain;margin-top: 60px; text-align: center"/>
            </div>
            <div class="col-md-12">
                <H1 class="change_password_text">Reset Password </H1>
                <section class="test-form">
                    <div class="row">

                        <?php if ($res): ?>
                            <form role="form" id="resetPassword" name="resetPassword" method="post" action=""
                                  onsubmit="return false" autocomplete="off">

                                <div class="clearfix"></div>
                                <fieldset class="form-group">
                                    <label for="password" class="floating">New password</label>
                                    <input type="password" class="form-control" name="password" id="password"
                                           placeholder="New Password"></fieldset>

                                <fieldset class="form-group">
                                    <label for="cfmpassword" class="floating">Confirm password</label>
                                    <input type="password" class="form-control" name="cfmpassword" id="cfmpassword"
                                           placeholder="Confirm Password"/></fieldset>

                                <div class="checkbox"></div>

                                <input type="hidden" id="user_id" name="userId" value="<?php echo $id; ?>">
                                <div style="display: none;text-align: center;color:red;" id="error_msg"><p>Password does
                                        not
                                        match</p></div>
                                <div style="display: none;text-align: center;color:red;" id="legth_error_msg"><p>
                                        Password must
                                        be at least 8 characters long</p></div>
                                <div style="display: none;text-align: center;color:red;" id="password_empty"><p>
                                        Please enter your new password and confirm password.</p></div>
                                <div class="row">
                                    <div class="col-sm-12 ">
                                        <input type="submit" class="btn btn-primary btn-block" style="width:100%;"
                                               name="passwordSubmit"
                                               id="passwordSubmit" value="Submit">
                                        </input>
                                    </div>
                                </div>
                            </form>

                        <?php else: ?>
                            <?php if ($msg == ""): ?>
                                <div id="expired"> Your password reset request has been expired . Please try again
                                    later
                                </div>
                            <?php else: ?>
                                <div id="expired"> <?php echo $msg ?><br/><br/>
                                    <!--  <a href="login" class="text-center">Login
                                          ?</a>--->
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
            </div>
        </div>
        <div>
            <div></div>
        </div>
        <!-- </div>--->
    </div>
    </div>
</section>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo base_url() ?>assets/js/bootstrap.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/jquery-ui.js"></script>
<script src="<?php echo base_url() ?>assets/js/scripts.js"></script>
<!---<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>--->

<script src="<?php echo base_url() ?>/assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url() ?>/assets/js/bootbox.min.js"></script>
<script src="<?php echo base_url() ?>project_scripts/CommonScript.js"></script>
<script src="<?php echo base_url() ?>project_scripts/UserScript.js"></script>
<script src="<?php echo base_url() ?>project_scripts/ReadyScripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<!-- Forgot password -->
<script>

    $("#resetPassword").submit(function (event) {
        event.preventDefault();

        if ($("#resetPassword").valid() === true) {
            $.ajax({
                url: BASEURL + "UserController/resetPasswordAction",
                dataType: "json",
                type: "post",
                data: {
                    "password": $("#cfmpassword").val(),
                    "id": $("#user_id").val()
                },
                success: function (response) {

                    $('#forgot_body').html(response);

                },
                error: function (response) {

                    $('#forgot_body').html(response);
                }
            });
        }

    });

</script>
</body>
</html>


