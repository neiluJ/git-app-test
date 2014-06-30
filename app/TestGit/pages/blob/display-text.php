<pre id="editor"><?php echo $this->_helper->escape($this->blob->getContent()); ?></pre>

<!-- load ace -->
<script src="<?php echo str_replace('/index.php', '/', $vh->url()); ?>js/ace-min/ace.js" type="text/javascript"></script>
<!-- load ace language tools -->
<script type="text/javascript">
    $(function() {
        // trigger extension
        var editor = ace.edit("editor");
        editor.setTheme("ace/theme/github");
        editor.setAutoScrollEditorIntoView(true);
        editor.setReadOnly(true);
        <?php if (!empty($this->language)): ?>
        editor.session.setMode("ace/mode/<?php echo $this->language; ?>");
        <?php endif; ?>
        editor.setShowPrintMargin(false);
        editor.setOptions({
            maxLines: editor.getSession().getScreenLength()
        });
    })
</script>
<p>Download this file: <a href="<?php echo $this->_helper->url('BlobRaw', array('name' => $this->name, 'path' => $this->path, 'branch' => $this->branch), true); ?>">click here</a></p>
