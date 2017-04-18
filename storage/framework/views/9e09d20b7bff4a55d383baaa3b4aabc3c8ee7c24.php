 
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
                       <div class="panel panel-cascade">
                          <div class="panel-body ">
                              <div class="row">  
                                      <?php echo Form::model($user, ['route' => ['user.store'],'class'=>'form-horizontal','id'=>'users_form']); ?>

                                        <?php echo $__env->make('packages::users.user.form', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                      <?php echo Form::close(); ?>

                              </div>
                          </div>
                    </div>
                </div>            
              </div>  
            <!-- Main row --> 
          </section><!-- /.content -->
      </div> 
<?php $__env->stopSection(); ?>

<?php echo $__env->make('packages::layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>