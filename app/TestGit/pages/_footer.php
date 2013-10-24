    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('.repo-nav ul li a').tooltip({container:'body'});
        });
    </script>  
    <script src="<?php echo str_replace('/index.php', '', $this->_helper->url()); ?>/js/highlight.js/highlight.pack.js"></script>
    <script src="<?php echo str_replace('/index.php', '', $vh->url()); ?>/js/bootstrap.min.js"></script>
  </body>
</html>