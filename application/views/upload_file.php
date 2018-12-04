<h1>Upload file ke Amazon S3</h1><br/>
<?php echo form_open_multipart('amazon/upload_file') ?>
File :<?php echo form_upload('file'); ?> <?php echo form_submit('Submit', 'Upload'); ?>
<?php echo form_close(); ?>