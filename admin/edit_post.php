<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH.'/includes/auth_validate.php';

// Sanitize if you want
$post_id = filter_input(INPUT_GET, 'post_id', FILTER_VALIDATE_INT);
$operation = filter_input(INPUT_GET, 'operation', FILTER_SANITIZE_STRING); 
($operation == 'edit') ? $edit = true : $edit = false;
$db = getDbInstance();

// Handle update request. As the form's action attribute is set to the same script, but 'POST' method, 
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    // Get post id form query string parameter.
    $post_id = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_STRING);

    // Get input data
    $data_to_db = filter_input_array(INPUT_POST);

    // Insert user and timestamp
    $data_to_db['author_id'] = $_SESSION['user_id'];
    $data_to_db['updated_at'] = date('Y-m-d H:i:s');

    $db = getDbInstance();
    $db->where('id', $post_id);
    $stat = $db->update('posts', $data_to_db);

    if ($stat)
    {
        $_SESSION['success'] = 'Post updated successfully!';
        // Redirect to the listing post
        header('Location: posts.php');
        // Important! Don't execute the rest put the exit/die.
        exit();
    }
}

// If edit variable is set, we are performing the update operation.
if ($edit)
{
    $db->where('id', $post_id);
    // Get data to pre-populate the form.
    $post = $db->getOne('posts');
}
?>
<?php include BASE_PATH.'/includes/header.php'; ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Update Post</h2>
        </div>
    </div>
    <!-- Flash messages -->
    <?php include BASE_PATH.'/includes/flash_messages.php'; ?>
    <form class="form" action="" method="post" id="post_form" enctype="multipart/form-data">
        <?php include BASE_PATH.'/forms/post_form.php'; ?>
    </form>
</div>
<?php include BASE_PATH.'/includes/footer.php'; ?>