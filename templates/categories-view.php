<?php

require_once dirname(__DIR__, 4) . '/wp-load.php';
$form_handler_path = plugin_dir_url(__FILE__) . '../includes/controllers/categories_form_handler.php';

// nonce_url
$nonce_form_handler_url = wp_nonce_url($form_handler_path);

global $wpdb;
$Category = new Db_category($wpdb);
$result = $Category->fetch_categories();
$filtered_result = array_merge(
    array_filter($result, fn($row) => $row->parent_id === null),
    array_filter($result, fn($row) => $row->parent_id !== null)
);

// Modal to display query success or failure based on url query string
if (isset($_GET['query_status'])) {
    $s_status = esc_attr($_GET['query_status']);
    $output = $s_status == "success" ? "success" : "failure";
    echo "<div id='modal' class='{$output}'>
            <p>{$output}</p>
        </div>";
}

?>

<div id="container" class="wrap">
    <h3 id="categoriesHeader" class="wrap">Manage Categories</h3>
    <!-- modal controls -->
    <div id="controlsContainer" class="wrap formContainer">
        <button id="category" class="ctrlButton" value="category">Create a category</button>
        <?php 
            if (!empty($result)) : 
        ?>
            <button id="subcategory" class="ctrlButton" value="subcategory">Create a subcategory</button>
        <?php endif; ?>
    </div>

    <!-- categories modal -->
    <div id="categoryContainer" class="wrap hidden formContainer">
        <h4>Add a category</h4>
        <form action="<?php echo $nonce_form_handler_url; ?>" method="POST">
            <input placeholder="category" type="text" class="inputField" name="category">
            <input type="submit" class="submitButton" value="submit" name="submit-category">
        </form>
        <button class="ctrlButton">back</button>
    </div>

    <!-- subcategories modal -->
    <?php 
        if (!empty($result)) : 
    ?>
    <div id="subcategoryContainer" class="wrap hidden formContainer">
        <h4>Add a subcategory</h4>
        <form action="<?php echo $nonce_form_handler_url; ?>" method="POST">
            <select name="category_id" id="selectBox" class="inputField">
                <?php 
                    foreach($result as $row) :
                        if ($row->parent_id == null) : ?>
                            <option  value="<?php echo esc_attr($row->id) ?>"><?php echo esc_html($row->category) ?></option>
                        <?php endif ?>
                    <?php endforeach; ?>
                </select>
                <input placeholder="subcategory" type="text" class="inputField" name="subcategory">
                <input type="submit" class="submitButton" value="submit" name="submit-subcategory">
            </form>
            <button class="ctrlButton">back</button>
        </div>
        <?php endif; ?>

        <div id="updateContainer" class="wrap hidden formContainer">
            <h4>Edit category</h4>
            <form action="<?php echo $nonce_form_handler_url; ?>" method="POST">
                <input readonly hidden type="number" class="inputField idInputField" name="id" id="idInputField">
                <input type="text" class="inputField nameInputField" id="nameInputField" name="category">
                <input type="submit" class="submitButton" value="update" name="update-category">
            </form>
            <button class="ctrlButton">cancel</button>
        </div>

        <div id="deleteContainer" class="wrap hidden formContainer">
            <h4>Delete category</h4>
            <form action="<?php echo $nonce_form_handler_url; ?>" method="POST">
                <input readonly hidden type="number" class="inputField idInputField" name="id" id="idInputField">
                <input readonly type="text" class="inputField nameInputField" id="nameInputField">
                <input type="submit" class="submitButton" value="delete" name="delete-category">
            </form>
            <button class="ctrlButton">cancel</button>
        </div>

        <!-- categories table -->
        <div id="categoriesTableContainer" class="wrap">
            <table>
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Subcategories</th>
                        <th>Controls</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $counter = 0;
                        foreach($filtered_result as $row) : 
                    ?>
                        <tr>
                            <td class="idTd" value="<?php echo esc_attr($row->id) ?>"> <?php echo ++$counter ?> </td>
                            <td class="typeTd <?php echo esc_attr($row->parent_id) == null ? "tdCategory" : "tdSubcategory" ?>" > <?php echo esc_html($row->parent_id) == null ? "category" : "subcategory" ?> </td>
                            <td class="categoryTd"> <?php echo esc_attr($row->category) ?> </td>
                            <td> 
                                <?php 
                                    $subcategories = $Category->fetch_sub_categories($row->id);
                                    if ($subcategories) { ?>
                                        <?php foreach($subcategories as $subcategory) : ?>
                                            <?php echo "• " . esc_html($subcategory->category) ?> <br>
                                        <?php endforeach; ?>
                                    <?php } else echo "❌"; ?>
                            </td>
                            <td> 
                                <button class="editButton" value="<?php $nonce_form_handler_url; ?>">Edit</button>
                                <button class="deleteButton" value="<?php $nonce_form_handler_url; ?>">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                </tbody>
            </table>
        </div>
</div>

