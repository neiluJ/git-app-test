<?php
/*
 * This dirty template is supposed to be replaced by your own!
 * PS: dirty is not a styleguide :P
 */
use Nitronet\Comments\Controllers\Thread;

function findCommentById($id, $comments)
{
    foreach ($comments as $comment) {
        if ($comment->getId() == $id) {
            return $comment;
        }
    }

    return null;
}

function commentHasChildren($target, $comments)
{
    foreach ($comments as $comment) {
        if ($comment->getParentId() == $target->getId()) {
            return true;
        }
    }

    return false;
}

function getChildComments($target, $comments)
{
    $result = array();
    foreach ($comments as $comment) {
        if ($comment->getParentId() == $target->getId()) {
            $result[] = $comment;
        }
    }

    return $result;
}

function printComment($comment, $comments, $type, $helper)
{
    $author = $comment->getAuthor()->get();
    $hasAuthor = ($author instanceof \TestGit\Model\User\User);
    if ($hasAuthor) {
        $fn = $author->getFullname();
        $un = $author->getUsername();
        $displayName = (empty($fn) ? $un : $fn);
    } else {
        $displayName = $comment->getAuthorName();
    }
?>
    <li id="comment-<?php echo $comment->getId(); ?>" class="comment">
        <article class="comment-body">
            <div class="comment-meta">
                <div class="comment-author vcard">
                    <img alt="<?php echo htmlentities($displayName, ENT_QUOTES, "utf-8"); ?>" src="//0.gravatar.com/avatar/<?php echo md5($comment->getAuthorEmail()); ?>?s=25&amp;d=identicon" class="avatar" height="25" width="25" />
                    <b class="fn"><?php if ($hasAuthor): ?><a href="<?php echo $helper->url('Profile', array('username' => $un)); ?>" rel="nofollow" class="url"><?php endif; ?><?php echo $helper->escape($displayName); ?><?php if ($hasAuthor): ?></a><?php endif; ?></b>
                    <?php $date = new DateTime($comment->getCreatedOn()); ?>
                    <time datetime="<?php echo $date->format(DateTime::ATOM); ?>"><?php echo $date->format(DateTime::COOKIE); ?></time>
                    <?php if ($type == Thread::TYPE_NORMAL && $comment->getParentId() != null): ?>
                        <small>(reply to <b><?php echo htmlentities(findCommentById($comment->getParentId(), $comments)->getAuthorName(), ENT_QUOTES, "utf-8"); ?></b>)</small>
                    <?php endif; ?>
                </div><!-- .comment-author -->
            </div><!-- .comment-meta -->
            <div class="comment-content">
                <p><?php echo htmlentities($comment->getContents(), ENT_QUOTES, "utf-8"); ?></p>
            </div><!-- .comment-content -->
        </article><!-- .comment-body -->
<?php if ($type == Thread::TYPE_THREADED && commentHasChildren($comment, $comments)): ?>
        <ol class="comment-list thread">
            <?php
                $childs = getChildComments($comment, $comments);
                foreach ($childs as $comment2) {
                    printComment($comment2, $comments, $type, $helper);
                }
            ?>
        </ol><!-- .comment-list -->
<?php endif; ?>
    </li>
<?php
}
?>
<?php if (!count($this->comments)): ?>
<p class="no-comment">There are no comments.</p>
<?php else: ?>
<ol class="comment-list">
<?php
// main loop
foreach ($this->comments as $comment) {
  if (($this->type == Thread::TYPE_THREADED && $comment->getParentId() == null) || $this->type == Thread::TYPE_NORMAL) {
      printComment($comment, $this->comments, $this->type, $this->_helper);
  }
}
?>
</ol><!-- .comment-list -->
<?php endif; ?>