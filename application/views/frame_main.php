<!DOCTYPE html>
<html>
<head>
    <?php $this->load->view('header'); ?>
</head>
<body>
<?php $this->load->view('fragment_navbar'); ?>
<div class="container-fluid">
    <div class="row">
        <?php $this->load->view('fragment_menu'); ?>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <?php $this->load->view('fragment_notice'); ?>
            <?php if (isset ($PageTitle)) : ?>
                <div class="row row-p0m0">
                    <div class="col-md-3 col-md-red"><h2><?php echo $PageTitle; ?></h2></div>
                </div>
            <?php endif; ?>
            <?php $this->load->view($TargetPage); ?>
        </div>
    </div>
</div>
<?php $this->load->view('footer'); ?>
</body>
</html>