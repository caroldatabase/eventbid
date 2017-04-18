<?php $__env->startSection('content'); ?> 
<?php echo $__env->make('packages::partials.main-header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<!-- Left side column. contains the logo and sidebar -->
<?php echo $__env->make('packages::partials.main-sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper"> 
 <?php echo $__env->make('packages::partials.breadcrumb', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
      <div class="row">
          <div class="col-md-12">
            <div class="col-md-12"> 
               <div class="row">
                    <div class="box">
                        <?php if(Session::has('flash_alert_notice')): ?>
                            <div class="alert alert-success alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    <i class="icon fa fa-check"></i>  
                                    <?php echo e(Session::get('flash_alert_notice')); ?> 
                            </div>
                        <?php endif; ?> 
                        <div class="box-body table-responsive no-padding "  >
                           <div class="col-md-12">
                                 <div class="alert alert-default alert-dismissable">
                                 <h1> <i class="fa fa-warning text-yellow"></i>  <span style="color:#f39c12">  <?php echo e(isset($error_msg) ? $error_msg : 'Oops! The page you requested  was found.'); ?> </span></h1>
                            </div>
                            <div class="col-md-12 ">
                            </div>
                           </div>
                        </div>
                    </div>            
                </div>    
            </div>
        </div>
      </div>         
    <!-- Main row --> 
  </section><!-- /.content -->
</div> 
<style>
  .btn.btn-block.btn-primary {
margin-bottom: 3px;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('packages::layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>