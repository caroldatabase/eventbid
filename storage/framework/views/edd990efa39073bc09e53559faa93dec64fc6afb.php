
<div class="col-md-6">

    <div class="form-group<?php echo e($errors->first('category_name', ' has-error')); ?>">
        <label class="col-lg-4 col-md-4 control-label"> Category Name <span class="error">*</span></label>
        <div class="col-lg-8 col-md-8"> 
            <?php echo Form::select('category_name', $category_list, null, ['class' => 'form-control form-cascade-control input-small']); ?>

            <span class="label label-danger"><?php echo e($errors->first('category_name', ':message')); ?></span>
        </div>
    </div> 

    

    <div class="form-group<?php echo e($errors->first('sub_category_name', ' has-error')); ?>">
        <label class="col-lg-4 col-md-4 control-label">Sub category name</label>
        <div class="col-lg-8 col-md-8"> 
            <?php echo Form::text('sub_category_name',null, ['class' => 'form-control form-cascade-control input-small']); ?>

            <span class="label label-danger"><?php echo e($errors->first('sub_category_name', ':message')); ?></span>
            <?php if(Session::has('flash_alert_notice')): ?> 
            <span class="label label-danger">

                <?php echo e(Session::get('flash_alert_notice')); ?> 

            </span><?php endif; ?>
        </div>
    </div> 
     
    
    <div class="form-group">
        <label class="col-lg-4 col-md-4 control-label"></label>
        <div class="col-lg-8 col-md-8">

            <?php echo Form::submit(' Save ', ['class'=>'btn  btn-primary text-white','id'=>'saveBtn']); ?>


            <a href="<?php echo e(route('category')); ?>">
            <?php echo Form::button('Back', ['class'=>'btn btn-warning text-white']); ?> </a>
        </div>
    </div>

</div> 