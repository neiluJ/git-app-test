<?php 
use dflydev\markdown\MarkdownExtraParser;
$parser = new MarkdownExtraParser();
?>
<div class="markdown">
<?php echo $parser->transform($this->blob->getContent());  ?>
</div>