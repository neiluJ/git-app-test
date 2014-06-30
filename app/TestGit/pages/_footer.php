    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('.repo-nav ul li a').tooltip({container:'body'});
        });
    </script>  
    <script src="<?php echo str_replace('/index.php', '/', $vh->url()); ?>js/bootstrap.min.js"></script>
    <script src="<?php echo str_replace('/index.php', '/', $vh->url()); ?>js/hogan-2.0.0.js"></script>
    <script src="<?php echo str_replace('/index.php', '/', $vh->url()); ?>js/typeahead.min.js"></script>
    </body>
</html>