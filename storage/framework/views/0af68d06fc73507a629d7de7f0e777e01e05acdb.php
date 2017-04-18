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
                    <div class="panel panel-cascade">
                        <div class="panel-body ">
                            <div class="row">
                                <div class="box">
                                    <div class="box-header">
                                        <form action="<?php echo e(route('user')); ?>" method="get">
                                            <div class="col-md-3">
                                                <select name="status" class="form-control">
                                                    <option value="">Sort by Status</option>
                                                    <option value="active" <?php if($status==='active'): ?> selected  <?php endif; ?>>Active</option>
                                                    <option value="inActive" <?php if($status==='inActive'): ?> selected  <?php endif; ?>>Inactive</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input value="<?php echo e((isset($_REQUEST['search']))?$_REQUEST['search']:''); ?>" placeholder="search by Name/Email" type="text" name="search" id="search" class="form-control" >
                                            </div>
                                            <div class="col-md-2">
                                                <input type="submit" value="Search" class="btn btn-primary form-control">
                                            </div>
                                           
                                        </form>
                                         <div class="col-md-2">
                                             <a href="<?php echo e(route('user')); ?>">   <input type="submit" value="Reset" class="btn btn-default form-control"> </a>
                                        </div>
                                       <div class="col-md-2 pull-right">
                                            <div style="width: 150px;" class="input-group"> 
                                                <a href="<?php echo e(route('user.create')); ?>">
                                                    <button class="btn  btn-primary"><i class="fa fa-user-plus"></i> Add Student</button> 
                                                </a>
                                            </div>
                                        </div> 
                                    </div><!-- /.box-header -->

                                    
                                    <?php if(Session::has('flash_alert_notice')): ?>
                                         <div class="alert alert-success alert-dismissable" style="margin:10px">
                                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                          <i class="icon fa fa-check"></i>  
                                         <?php echo e(Session::get('flash_alert_notice')); ?> 
                                         </div>
                                    <?php endif; ?>
                                      
                                   <div class="box-body table-responsive no-padding" >
                                        <table class="table table-hover table-condensed">
                                            <thead><tr>
                                                    <th>Sno</th>
                                                    <th>Full Name</th>
                                                    <th>Email</th>
                                                    <th>User Type</th>  
                                                    <th>Signup Date</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                                <?php if(count($users)==0): ?>
                                                    <tr>
                                                      <td colspan="7">
                                                        <div class="alert alert-danger alert-dismissable">
                                                          <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                                          <i class="icon fa fa-check"></i>  
                                                          <?php echo e('Record not found. Try again !'); ?>

                                                        </div>
                                                      </td>
                                                    </tr>
                                                  <?php endif; ?>
                                                <?php foreach($users as $key => $user): ?>  
                                             <thead>
                                              <tbody>    
                                                <tr>
                                                    <td><?php echo e(++$key); ?></td>
                                                    <td><?php echo e($user->name); ?></td>
                                                    <td><?php echo e($user->email); ?></td>
                                                    <td><?php echo e(($user->role_type==1)?'Professor':'Student'); ?> </td> 
                                                    <td>
                                                        <?php echo Carbon\Carbon::parse($user->created_at)->format('m-d-Y H:i:s A');; ?>

                                                    </td>
                                                    <td>
                                                        <span class="label label-<?php echo e(($user->status==1)?'success':'warning'); ?> status" id="<?php echo e($user->id); ?>"  data="<?php echo e($user->status); ?>"  onclick="changeStatus(<?php echo e($user->id); ?>,'user')" >
                                                            <?php echo e(($user->status==1)?'Active':'Inactive'); ?>

                                                        </span>
                                                    </td>
                                                    <td> 
                                                        <a href="<?php echo e(route('student.edit',$user->id)); ?>">
                                                            <i class="fa fa-fw fa-pencil-square-o" title="edit"></i> 
                                                        </a>

                                                        <?php echo Form::open(array('class' => 'form-inline pull-left deletion-form', 'method' => 'DELETE',  'id'=>'deleteForm_'.$user->id, 'route' => array('student.destroy', $user->id))); ?>

                                                        <button class='delbtn btn btn-danger btn-xs' type="submit" name="remove_levels" value="delete" id="<?php echo e($user->id); ?>"><i class="fa fa-fw fa-trash" title="Delete"></i></button>
                                                        
                                                         <?php echo Form::close(); ?>


                                                    </td>
                                                </tr>
                                                <?php endforeach; ?> 
                                            </tbody></table>
                                    </div><!-- /.box-body --> 
                                    <div class="center" align="center">  <?php echo $users->appends(['search' => isset($_GET['search'])?$_GET['search']:''])->render(); ?></div>
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

<?php $__env->stopSection(); ?>

<?php echo $__env->make('packages::layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>