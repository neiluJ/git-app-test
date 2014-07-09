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
        editor.setFontSize("14px");
        <?php if (!empty($this->language)): ?>
        editor.session.setMode("ace/mode/<?php echo $this->language; ?>");
        <?php endif; ?>
        editor.setShowPrintMargin(false);
        editor.setOptions({
            maxLines: editor.getSession().getScreenLength()
        });
    })
</script>