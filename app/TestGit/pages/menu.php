<?php
$vh = $this->_helper;
$active = $this->active;
?>
<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
        <a class="navbar-brand" href="<?php echo $vh->url(); ?>" style="padding-right: 30px;">
            <?php echo $vh->escape($vh->appTitle()); ?>
        </a>
    </div>
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav navbar-left">
        <li<?php if($active == "repositories"): ?> class="active"<?php endif ?>><a href="<?php echo $vh->url('Repositories', array(), true); ?>"><span class="octicon octicon-repo"></span> Repositories</a></li>
        <li<?php if($active == "users"): ?> class="active"<?php endif ?>><a href="<?php echo $vh->url('Users', array(), true); ?>"><i class="octicon octicon-organization"></i> Users</a></li>
          <?php echo $vh->embed('ChatMenu'); ?>
      <li>
<form class="navbar-form" style="padding:0; margin-left: 10px;clear:left" method="get" action="<?php echo $this->_helper->url('Search'); ?>">
<div class="form-group">
  <input type="text" name="q" placeholder="Search Repositories or Commits" class="form-control git-search" style="width: 300px"> <a class="adv-search" href="<?php echo $this->_helper->url('Search'); ?>"><i class="glyphicon glyphicon-search"></i></a>
</div>
</form>
        </li>
      </ul>
     <?php echo $vh->embed('UserMenu'); ?>
    </div>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('input.git-search').typeahead([
      {
        name: 'usser',
        prefetch: {url: "<?php echo $this->_helper->url('SearchUsers'); ?>", ttl: 5, filter: function(obj) { return obj.searchResults; }},
         template: [
        '<p class="repo-name"><i class="{{#organization}}octicon octicon-organization{{/organization}}{{^organization}}glyphicon glyphicon-user{{/organization}}"></i> {{value}}</p>',
        '<p class="repo-desc">{{description}}</p>',
        ].join(''),
        engine: Hogan,
        limit:10,
        header: '<span class="dropdown-header">USERS</span>',
        cache:false
      },
      {
        name: 'repositoris',
        prefetch: {url: "<?php echo $this->_helper->url('SearchRepositories'); ?>", ttl: 5, filter: function(obj) { return obj.searchResults; }},
         template: [
        '<p class="repo-name"><i class="octicon octicon-{{#fork}}repo-forked{{/fork}}{{^fork}}repo{{/fork}}"></i>{{#private}} <i class="octicon octicon-lock"></i> {{/private}} {{value}}</p>',
        '<p class="repo-desc">{{description}}</p>',
        ].join(''),
        engine: Hogan,
        limit:10,
        header: '<span class="dropdown-header">REPOSITORIES</span>',
        cache:false
      },
      {
        name: 'commits',
        remote: {cache: false, url: "<?php echo $this->_helper->url('SearchCommits'); ?>?q=%QUERY", ttl: 5, filter: function(obj) { return obj.searchResults; }},
        template: [
            '<p class="commit-date">{{date}}</p>',
            '<p class="repo-name"><i class="octicon octicon-git-commit"></i> {{shortHash}}</p>',
            '<p class="commit-details"><strong>{{repoName}}</strong> by <strong>{{committer}}</strong></p>',
            '<p class="commit-txt">{{message}}</p>',
        ].join(''),
        engine: Hogan,
        valueKey: "name",
        limit:5,
        header: '<span class="dropdown-header">COMMITS</span>'
      },
    ]).bind('typeahead:selected', function (obj, datum) {
        if (datum != undefined && datum.url != undefined) {
            window.location = datum.url;
        }
    });
    
    $('input.git-search').focus();
});  
</script>
<div class="progress progress-striped active" id="progress">
  <div class="progress-bar"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
    <span class="sr-only">Please wait</span>
  </div>
</div>
