<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar" style="height: auto;">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        <img alt="User Image" class="img-circle" src="<?php echo e(URL::asset('public/assets/dist/img/user2-160x160.jpg')); ?>">
      </div>
      <div class="pull-left info">
        <p>Inesport</p>
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
<!--
      <li class="treeview <?php echo e((isset($page_action) && $page_title=='Corporate Profile')?"active":''); ?> ">
        <a href="#">
          <i class="fa fa-user"></i>
          <span>Manage Corporate Profile</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
           <li class="<?php echo e((isset($page_action) && $page_action=='View Corporate Profile')?"active":''); ?>"><a href="<?php echo e(route('corporateProfile')); ?>"><i class="fa  fa-list"></i> Corporate Profile </a></li>
        </ul>
      </li> 
      
      <li class="treeview <?php echo e((isset($page_action) && $page_title=='Monthly Evaluation Report')?"active":''); ?> ">
        <a href="<?php echo e(url('admin/corporate-monthly-report')); ?>">
          <i class="fa fa-user"></i>
          <span>Monthly Evaluation Report</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
           <li class="<?php echo e((isset($page_action) && $page_action=='Corporate monthly report')?"active":''); ?>"><a href="<?php echo e(url('admin/corporate-monthly-report')); ?>"><i class="fa  fa-list"></i> View Report </a></li>
        </ul>
      </li> 



      <li class="treeview <?php echo e((isset($page_action) && $page_title=='Candidate Directory')?"active":''); ?> ">
        <a href="#">
          <i class="fa fa-user"></i>
          <span>Candidate Directory</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
           <li class="<?php echo e((isset($page_action) && $page_action=='View Directory')?"active":''); ?>"><a href="<?php echo e(url('admin/condidateDirectory')); ?>"><i class="fa  fa-list"></i>Candidate Directory</a></li>
        </ul>
      </li>  
-->    
  <li class="treeview <?php echo e((isset($page_action) && $page_title=='User')?"active":''); ?> ">
        <a href="#">
          <i class="fa fa-user"></i>
          <span>Manage Users</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
          <li class="<?php echo e((isset($page_action) && $page_action=='Create User')?"active":''); ?>" ><a href="<?php echo e(route('user.create')); ?>"><i class="fa fa-user-plus"></i> Create User</a></li>
           <li class="<?php echo e((isset($page_action) && $page_action=='View User')?"active":''); ?>"><a href="<?php echo e(route('user')); ?>"><i class="fa  fa-list"></i> View User</a></li>
        </ul>
      </li>
 
      <li class="treeview <?php echo e((isset($page_action) && $page_title=='Category')?"active":''); ?> ">
        <a href="#">
          <i class="fa fa-user"></i>
          <span>Manage category</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
          <li class="<?php echo e((isset($page_action) && $page_action=='Create category')?"active":''); ?>" ><a href="<?php echo e(route('category.create')); ?>"><i class="fa fa-user-plus"></i> Create Category</a></li>
           <li class="<?php echo e((isset($page_action) && $page_action=='Create Sub-category')?"active":''); ?>" ><a href="<?php echo e(route('sub-category.create')); ?>"><i class="fa fa-user-plus"></i> Add Sub-category</a></li>
          <li class="<?php echo e((isset($page_action) && $page_action=='View Category')?"active":''); ?>"><a href="<?php echo e(route('category')); ?>"><i class="fa  fa-list"></i> View Category</a></li>
        </ul>
      </li>

      <li class="treeview <?php echo e((isset($page_action) && $page_title=='Article')?"active":''); ?> ">
        <a href="#">
          <i class="fa fa-user"></i>
          <span>Manage Article</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
          <li class="<?php echo e((isset($page_action) && $page_action=='Create Article')?"active":''); ?>" ><a href="<?php echo e(route('article.create')); ?>"><i class="fa fa-user-plus"></i> Create Article</a></li>
          <li class="<?php echo e((isset($page_action) && $page_action=='View Article')?"active":''); ?>"><a href="<?php echo e(route('article')); ?>"><i class="fa  fa-list"></i> View Article</a></li>
        </ul>
      </li>  
     <!-- 

      <li class="treeview <?php echo e((isset($page_action) && $page_title=='Rating Feedback')?"active":''); ?> ">
        <a href="#">
          <i class="fa fa-user"></i>
          <span>Manage Rating</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
           <li class="<?php echo e((isset($page_action) && $page_action=='Rating')?"active":''); ?>"><a href="<?php echo e(route('ratingFeedback')); ?>"><i class="fa  fa-list"></i> View Rating </a></li>
        </ul>
      </li>       

      <li class="treeview <?php echo e((isset($page_action) && $page_title=='Default Criteria')?"active":''); ?> ">
        <a href="#">
          <i class="fa fa-user"></i>
          <span>Default Evaluation Criteria</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            <li class="<?php echo e((isset($page_action) && $page_action=='Create Default Criteria')?"active":''); ?>" ><a href="<?php echo e(route('defaultCriteria.create')); ?>"><i class="fa fa-user-plus"></i> Create Default Criteria</a></li>
           <li class="<?php echo e((isset($page_action) && $page_action=='View Default Criteria')?"active":''); ?>"><a href="<?php echo e(route('defaultCriteria')); ?>"><i class="fa  fa-list"></i>Default Criteria </a></li>
        </ul>
      </li> 
-->

    </ul>
  </section>
  <!-- /.sidebar -->
</aside>
 
