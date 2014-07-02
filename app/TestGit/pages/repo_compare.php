<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

<div class="container">
    <div class="row" style="margin-top:40px;">
<?php $repoMenuActive = "compare"; include __DIR__ .'/_repository_left.php'; ?>
    <div class="col-md-10">

            <h3 style="margin-top:0"><a href="#" class="pull-right btn btn-success" disabled="disabled"><b class="octicon octicon-git-pull-request"></b> Create <strong>Pull Request</strong></a> Compare <i class="mega-octicon octicon-git-compare"></i></h3>

            <div class="row compare-head">
                <div class="col-md-5">
                    <h6>Base</h6>
                    <div class="compare-bulb">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon"><b class="octicon octicon-repo"></b></div>
                                <input type="text" readonly="readonly" class="form-control" value="<?php echo $vh->escape($this->entity->getFullname()); ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon"><b class="octicon octicon-git-branch"></b></div>
                                <input type="text" class="form-control" placeholder="Branch, Tag or Commit" />
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="col-md-2" style="text-align: center; color: #ddd; padding-top: 40px;">
                    <span class="mega-octicon octicon-ellipsis" style=""></span>
                </div>
                <div class="col-md-5">
                    <h6>Target</h6>
                    <div class="compare-bulb">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon"><b class="octicon octicon-repo"></b></div>
                                <select class="form-control">
                                    <?php foreach ($this->targets as $target): ?>
                                        <option value="<?php echo $target->getOwner()->getUsername(); ?>"<?php if($this->currentTarget == $target->getOwner()->getUsername()): ?> selected="selected"<?php endif; ?>><?php echo $vh->escape($target->getFullname()); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon"><b class="octicon octicon-git-branch"></b></div>
                                <input type="text" class="form-control" placeholder="Branch, Tag or Commit" />
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <input type="submit" class="btn btn-default pull-right" style="margin-top: 10px;" value="Compare" />
                </div>
            </div>
            <hr />
        </div>
    </div><!-- /row -->
</div><!-- /.container -->
<?php include __DIR__ .'/_footer.php'; ?>