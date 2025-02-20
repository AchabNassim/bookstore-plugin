<?php

require_once dirname(__DIR__, 4) . '/wp-load.php';
$form_handler_path = plugin_dir_url(__FILE__) . '../includes/controllers/book_form_handler.php';

// nonce_url
$nonce_form_handler_url = wp_nonce_url($form_handler_path);

global $wpdb;
$Book = new Db_book($wpdb);
$Category = new Db_category($wpdb);

$book_result = $Book->fetch_books();
$category_result = $Category->fetch_categories();

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
    <h3 id="booksHeader" class="wrap">Manage Books</h3>
    <?php
        if (empty($category_result)) { ?>
            <h4>Please create a category and add a subcategory before proceeding</h4>
    <?php } else { ?>

        <div id="controlsContainer" class="wrap formContainer">
            <button id="category" class="ctrlButton" value="book">Create a book</button>
        </div>

        <div id="bookContainer" class="wrap hidden formContainer">
            <h4>Add a Book</h4>
            <form class="submitForm" action="<?php echo $nonce_form_handler_url; ?>" method="POST" enctype="multipart/form-data">
                <input required placeholder="title" type="text" class="inputField titleInputField" name="title">
                <input required placeholder="author" type="text" class="inputField authorInputField" name="author">
                <input required placeholder="description" type="text" class="inputField descriptionInputField" name="description">
                <input required placeholder="price in $" type="number" class="inputField priceInputField" name="price">

                <select required name="category" id="categorySelectBox" class="inputField categoryInputField">
                    <option value="">Please select a category</option>
                    <?php 
                        foreach($category_result as $row) :
                            if ($row->parent_id === null) : ?>
                                <option data-category-id="<?php echo esc_attr($row->id) ?>" value="<?php echo esc_attr($row->category) ?>"><?php echo esc_html($row->category) ?></option>
                        <?php endif ?>
                    <?php endforeach; ?>
                </select>
                <select required name="subcategory" id="subcategorySelectBox" class="inputField subcategoryInputField hidden">
                    <option value="">Please select a subcategory</option>
                    <option value="None">❌</option>
                    <?php 
                        foreach($category_result as $row) :
                            if ($row->parent_id !== null) : ?>
                                <option data-parent-id="<?php echo esc_attr($row->parent_id) ?>" value="<?php echo esc_attr($row->category) ?>"><?php echo esc_html($row->category) ?></option>
                            <?php endif ?>
                            
                    <?php endforeach; ?>
                </select>
                <input type="file" class="inputField coverInputField" name="cover">
                <input type="submit" class="submitButton" value="submit" name="submit-book">
            </form>
            <button class="ctrlButton">back</button>
        </div>

        <div id="updateContainer" class="wrap hidden formContainer" enctype="multipart/form-data">
            <h4>Edit book</h4>
            <form class="submitForm" action="<?php echo $nonce_form_handler_url; ?>" method="POST" enctype="multipart/form-data">
                <input readonly hidden type="number" class="inputField idInputField" name="id" id="idInputField">        
                <input required placeholder="title" type="text" class="inputField titleInputField" name="title">
                <input required placeholder="author" type="text" class="inputField authorInputField" name="author">
                <input required placeholder="description" type="text" class="inputField descriptionInputField" name="description">
                <input required placeholder="price in $" type="number" class="inputField priceInputField" name="price">

                <select required name="category" id="categorySelectBox" class="inputField categoryInputField">
                    <option value="">Please select a category</option>
                    <?php 
                        foreach($category_result as $row) :
                            if ($row->parent_id === null) : ?>
                                <option data-category-id="<?php echo esc_attr($row->id) ?>" value="<?php echo esc_attr($row->category) ?>"><?php echo esc_html($row->category) ?></option>
                        <?php endif ?>
                    <?php endforeach; ?>
                </select>
                <select required name="subcategory" id="subcategorySelectBox" class="inputField subcategoryInputField hidden">
                    <option value="">Please select a subcategory</option>
                    <option value="None">❌</option>
                    <?php 
                        foreach($category_result as $row) :
                            if ($row->parent_id !== null) : ?>
                                <option data-parent-id="<?php echo esc_attr($row->parent_id) ?>" value="<?php echo esc_attr($row->category) ?>"><?php echo esc_html($row->category) ?></option>
                            <?php endif ?>
                    <?php endforeach; ?>
                </select>
                <input hidden readonly type="input" class="inputField urlInputField" name="cover_url">
                <input type="file" class="inputField coverInputField" name="cover">
                <input type="submit" class="submitButton" value="submit" name="update-book">
            </form>
            <button class="ctrlButton">cancel</button>
        </div>

        <div id="deleteContainer" class="wrap hidden formContainer">
            <h4>Delete book</h4>
            <form action="<?php echo $nonce_form_handler_url; ?>" method="POST">
                <input readonly hidden type="number" class="inputField idInputField" name="id" id="idInputField">
                <input readonly type="text" class="inputField titleInputField" id="nameInputField">
                <input readonly type="text" class="inputField authorInputField" id="nameInputField">
                <input type="submit" class="submitButton" value="delete" name="delete-book">
            </form>
            <button class="ctrlButton">cancel</button>
        </div>
    </div>
    <?php }; ?>

    <!-- books table -->
    <div id="booksTableContainer" class="wrap">
        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Subcategory</th>
                    <th>Cover name</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $counter = 0;
                    foreach($book_result as $row) : 
                ?>
                    <tr>
                        <td class="idTd" value="<?php echo esc_attr($row->id) ?>"> <?php echo ++$counter ?> </td>
                        <td class="titleTd" value="<?php echo esc_attr($row->title) ?>"> <?php echo esc_html($row->title) ?> </td>
                        <td class="authorTd" value="<?php echo esc_attr($row->author) ?>"> <?php echo esc_html($row->author) ?> </td>
                        <td class="descriptionTd" value="<?php echo esc_attr($row->description) ?>"> <?php echo esc_html($row->description) ?> </td>
                        <td class="priceTd" value="<?php echo esc_attr($row->price) ?>"> <?php echo esc_html($row->price) ?> </td>
                        <td class="categoryTd" value="<?php echo esc_attr($row->category) ?>"> <?php echo esc_html($row->category) ?> </td>
                        <td class="subcategoryTd" value="<?php echo esc_attr($row->subcategory) == null ? "None" : $row->subcategory ?>"> <?php echo esc_html($row->subcategory) == null ? "❌" : esc_html($row->subcategory) ?> </td>
                        <td class="coverTd" value="<?php echo esc_attr($row->cover_url) ?>"> 
                            <?php 
                                $cover_path = explode("/", $row->cover_url);
                                echo count($cover_path) < 2 ? "Default" : $cover_path[count($cover_path) - 1];
                            ?> 
                        </td>
                        <td> 
                            <button class="editButton" value="<?php echo $nonce_form_handler_url; ?>">Edit</button>
                            <button class="deleteButton" value="<?php echo $nonce_form_handler_url; ?>">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>