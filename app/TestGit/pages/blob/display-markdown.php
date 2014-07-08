<?php
use Michelf\MarkdownExtra;
?>
<div class="markdown">
<?php echo MarkdownExtra::defaultTransform($this->blob->getContent());  ?>
</div>