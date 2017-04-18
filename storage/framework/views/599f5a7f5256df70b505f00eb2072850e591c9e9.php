
<div class="col-md-6">

    <div class="form-group<?php echo e($errors->first('first_name', ' has-error')); ?>">
        <label class="col-lg-4 col-md-4 control-label"> First Name <span class="error">*</span></label>
        <div class="col-lg-8 col-md-8"> 
            <?php echo Form::text('first_name',null, ['class' => 'form-control form-cascade-control input-small']); ?> 
            <span class="label label-danger"><?php echo e($errors->first('first_name', ':message')); ?></span>
        </div>
    </div> 

    <div class="form-group<?php echo e($errors->first('last_name', ' has-error')); ?>">
        <label class="col-lg-4 col-md-4 control-label">Last Name</label>
        <div class="col-lg-8 col-md-8"> 
            <?php echo Form::text('last_name',null, ['class' => 'form-control form-cascade-control input-small']); ?>

            <span class="label label-danger"><?php echo e($errors->first('last_name', ':message')); ?></span>
        </div>
    </div>

    <div class="form-group<?php echo e($errors->first('email', ' has-error')); ?>">
        <label class="col-lg-4 col-md-4 control-label">Email</label>
        <div class="col-lg-8 col-md-8"> 
            <?php echo Form::email('email',null, ['class' => 'form-control form-cascade-control input-small']); ?>

            <span class="label label-danger"><?php echo e($errors->first('email', ':message')); ?></span>
            <?php if(Session::has('flash_alert_notice')): ?> 
            <span class="label label-danger">

                <?php echo e(Session::get('flash_alert_notice')); ?> 

            </span><?php endif; ?>
        </div>
    </div>
    
    <div class="form-group<?php echo e($errors->first('password', ' has-error')); ?>">
        <label class="col-lg-4 col-md-4 control-label">Password</label>
        <div class="col-lg-8 col-md-8">   
            <input type="password" name="password" id="password" class="form-control form-cascade-control input-small" value="">
            <span class="label label-danger"><?php echo e($errors->first('password', ':message')); ?></span>
        </div>
    </div>
   <!--  <div class="form-group<?php echo e($errors->first('position', ' has-error')); ?>">
        <label class="col-lg-4 col-md-4 control-label">Designation</label>
        <div class="col-lg-8 col-md-8"> 
           <select name="positionID" class="form-control form-cascade-control">
            <?php foreach($position as $key=>$value): ?>
            
            <option value="<?php echo e($value->id); ?>" <?php echo e(($value->id ==$user->positionID)?"selected":""); ?>><?php echo e($value->position_name); ?></option>
            <?php endforeach; ?>
            </select>
            <span class="label label-danger"><?php echo e($errors->first('positionID', ':message')); ?></span>
        </div>
    </div>     -->

     
    
    <div class="form-group">
        <label class="col-lg-4 col-md-4 control-label"></label>
        <div class="col-lg-8 col-md-8">

            <?php echo Form::submit(' Save ', ['class'=>'btn  btn-primary text-white','id'=>'saveBtn']); ?>


            <a href="<?php echo e(route('user')); ?>">
            <?php echo Form::button('Back', ['class'=>'btn btn-warning text-white']); ?> </a>
        </div>
    </div>

</div> 