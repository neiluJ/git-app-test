<?php if ($this->posted === true): ?>
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        Your comment has been posted !
    </div>
<?php elseif (!empty($this->status)): ?>
    <div class="alert alert-warning">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <?php
        if (is_string($this->status)) {
            echo htmlentities($this->status);
        } elseif (is_array($this->status)) {
            foreach ($this->status as $error) {
                echo htmlentities($error);
            }
        } else {
            echo "Something bad happened :(";
        }
    ?>
    </div>
<?php endif; ?>
<?php
    echo $this->renderer->render($this->formObj);
?>