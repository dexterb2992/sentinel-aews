<!-- Sidebar Menu -->
  <ul class="sidebar-menu">
    <li class="header"><!-- HEADER --></li>
    <!-- Optionally, you can add icons to the links -->
    <?php 
        if(get_current_user_id() > 0){ 
            get_sidebar_items();
        }
    ?>


  </ul><!-- /.sidebar-menu -->

<!-- /.sidebar -->