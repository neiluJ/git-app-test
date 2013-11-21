<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

    <div class="container">
        <?php $repoMenuActive = ''; include __DIR__ .'/_repository_header.php'; ?>
 
    <div class="row">
        <div class="col-sm-6" style="float:none; margin: 0 auto;">
            <div class="alert alert-info">This (empty) repository is ready for you!</div>
            
            <h3>Create a new repository</h3>

<pre>
touch README.md
git init
git add README.md
git commit -m "first commit"
git remote add origin <span class="git-clone-url"><?php echo $this->cloneSshUrl; ?></span>
git push -u origin master
</pre>

<h3>Push an existing repository</h3>

<pre>
git remote add origin <span class="git-clone-url"><?php echo $this->cloneSshUrl; ?></span>
git push -u origin master
</pre>


        </div>
    </div><!-- /row -->
    
    </div><!-- /.container -->

<script type="text/javascript">
$(document).ready(function() {
   $('#gitUrl').on("change", function(event) {
       $('.git-clone-url').html($('#gitUrl').html());
   });
});
</script>    
<?php include __DIR__ .'/_footer.php'; ?>