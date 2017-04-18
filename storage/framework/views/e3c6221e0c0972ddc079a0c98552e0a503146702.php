<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar" style="height: auto;">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        <img alt="User Image" class="img-circle" src="<?php echo e(URL::asset('public/assets/dist/img/user2-160x160.jpg')); ?>">
      </div>
      <div class="pull-left info">
        <p>API</p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>
    
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu">
      <li class="header">MAIN NAVIGATION</li>
      
      <li class="active treeview">
        <a href="<?php echo e(url('admin')); ?>">
          <i class="fa fa-dashboard"></i> <span>Dashboard</span> </i>
        </a>
          
      </li> 
      <li class="treeview <?php echo e((isset($page_action) && $page_title=='User' || $page_title=='Student' || $page_title=='Professor')?"active":''); ?> ">
        <?php echo e($page_title); ?>

        <a href="#">
          <i class="fa fa-user"></i>
          <span>Manage Users</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
          <li class="<?php echo e((isset($page_action) && $page_action=='Create User')?"active":''); ?>" ><a href="<?php echo e(route('user.create')); ?>"><i class="fa fa-user-plus"></i> Create User</a></li>
          <li class="<?php echo e((isset($page_action) && $page_action=='View User')?"active":''); ?>"><a href="<?php echo e(route('user')); ?>"><i class="fa  fa-list"></i> View All Users</a></li>
          <li class="<?php echo e((isset($page_action) && $page_action=='View Student')?"active":''); ?>"><a href="<?php echo e(route('student')); ?>"><i class="fa  fa-list"></i> View Students</a></li>
          <li class="<?php echo e((isset($page_action) && $page_action=='View Professor')?"active":''); ?>"><a href="<?php echo e(route('professor')); ?>"><i class="fa  fa-list"></i> View Professor</a></li>
        
          </ul>
      </li>

       <li class="treeview <?php echo e((isset($page_action) && $page_title=='Course')?"active":''); ?> ">
        <a href="#">
          <i class="fa fa-user"></i>
          <span>Manage Course</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
           <li class="<?php echo e((isset($page_action) && $page_action=='Create Course')?"active":''); ?>" ><a href="<?php echo e(route('course.create')); ?>"><i class="fa fa-user-plus"></i> Create Course</a></li>
           <li class="<?php echo e((isset($page_action) && $page_action=='View Course')?"active":''); ?>"><a href="<?php echo e(route('course')); ?>"><i class="fa  fa-list"></i> View Course</a></li>
        </ul>
      </li>


       <li class="treeview <?php echo e((isset($page_action) && $page_title=='Syllabus')?"active":''); ?> ">
        <a href="#">
          <i class="fa fa-user"></i>
          <span>Manage Syllabus</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
           <li class="<?php echo e((isset($page_action) && $page_action=='Create Syllabus')?"active":''); ?>" ><a href="<?php echo e(route('syllabus.create')); ?>"><i class="fa fa-user-plus"></i> Create Syllabus</a></li>
           <li class="<?php echo e((isset($page_action) && $page_action=='View Syllabus')?"active":''); ?>"><a href="<?php echo e(route('syllabus')); ?>"><i class="fa  fa-list"></i> View Syllabus</a></li>
        </ul>
      </li>


       <li class="treeview <?php echo e((isset($page_action) && $page_title=='Assignment')?"active":''); ?> ">
        <a href="#">
          <i class="fa fa-user"></i>
          <span>Manage Assignment</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
          <li class="<?php echo e((isset($page_action) && $page_action=='Create Assignment')?"active":''); ?>" ><a href="<?php echo e(route('assignment.create')); ?>"><i class="fa fa-user-plus"></i> Create Assignment</a></li>
           <li class="<?php echo e((isset($page_action) && $page_action=='View Assignment')?"active":''); ?>"><a href="<?php echo e(route('assignment')); ?>"><i class="fa  fa-list"></i> View Assignment</a></li>
        </ul>
      </li>


    </ul>
  </section>
  <!-- /.sidebar -->
</aside>
 
