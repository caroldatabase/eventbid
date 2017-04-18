 <section class="content-header" style="margin:0 17px 20px 16px">
    <h1>
      <?php echo e($page_title); ?>

      <small><?php echo e($page_action); ?></small>
        </h1>
        <ol class="breadcrumb">
          <li><a href="<?php echo e(url('admin')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active"><a href="<?php echo e(url('admin/'.$viewPage)); ?>"><?php echo e($page_title); ?></a></li>
          <li class="active"><?php echo e($page_action); ?></li>
        </ol> 
  </section>
   <section style="margin:15px 30px -30px 30px">
    <?php if(Input::has('error')): ?>
             <div class="alert alert-danger alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
             <h4> <i class="icon fa fa-check"></i>  
                Sorry! You are trying to access invalid URL.</h4>
             </div>
        <?php endif; ?>
  </section>