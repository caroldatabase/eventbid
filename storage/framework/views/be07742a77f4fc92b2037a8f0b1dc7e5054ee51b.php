
<div class="col-md-6">


     <div class="form-group<?php echo e($errors->first('course_id', ' has-error')); ?>">
        <label class="col-lg-4 col-md-4 control-label">Choose Course </label>
        <div class="col-lg-8 col-md-8"> 
           <select name="course_id" class="form-control form-cascade-control">
            <?php foreach($course as $key=>$value): ?>
            
            <option value="<?php echo e($value->id); ?>" <?php echo e(($value->id ==$syllabus->course_id)?"selected":""); ?>><?php echo e($value->course_name); ?></option>
            <?php endforeach; ?>
            </select>
            <span class="label label-danger"><?php echo e($errors->first('course_id', ':message')); ?></span>
        </div>
    </div>  

    <div class="form-group<?php echo e($errors->first('syllabus_title', ' has-error')); ?>">
        <label class="col-lg-4 col-md-4 control-label"> Syllabus Title <span class="error">*</span></label>
        <div class="col-lg-8 col-md-8"> 
            <?php echo Form::text('syllabus_title',null, ['class' => 'form-control form-cascade-control input-small']); ?> 
            <span class="label label-danger"><?php echo e($errors->first('syllabus_title', ':message')); ?></span>
        </div>
    </div> 

     <div class="form-group<?php echo e($errors->first('syllabus_description', ' has-error')); ?>">
        <label class="col-lg-4 col-md-4 control-label"> Syllabus Description <span class="error">*</span></label>
        <div class="col-lg-8 col-md-8"> 
            <?php echo Form::text('syllabus_description',null, ['class' => 'form-control form-cascade-control input-small']); ?> 
            <span class="label label-danger"><?php echo e($errors->first('syllabus_description', ':message')); ?></span>
        </div>
    </div> 

     <div class="form-group<?php echo e($errors->first('grade_weight', ' has-error')); ?>">
        <label class="col-lg-4 col-md-4 control-label">Grade weight *</label>
        <div class="col-lg-8 col-md-8"> 
            <?php echo Form::text('grade_weight',null, ['class' => 'form-control form-cascade-control input-small']); ?>

            <span class="label label-danger"><?php echo e($errors->first('grade_weight', ':message')); ?></span>
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


            <a href="<?php echo e(route('syllabus')); ?>">
            <?php echo Form::button('Back', ['class'=>'btn btn-warning text-white']); ?> </a>
        </div>
    </div>

</div> 