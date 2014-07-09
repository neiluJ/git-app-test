<?php 
if (!isset($vh)) {
    $vh = $this->_helper;
}
?>
<img src="<?php echo $vh->url('BlobRaw', array('name' => $this->name, 'path' => $this->path, 'branch' => $this->branch), true); ?>" alt="<?php echo $this->path; ?>" title="<?php echo $this->path; ?>" />
