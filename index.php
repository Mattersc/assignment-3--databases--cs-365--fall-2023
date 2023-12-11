<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset= "UTF-8">
    <title> Password Manager for Database</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<header>
    <h1>The Password Manager</h1>
</header>

<?php

require_once "includes/config.php";
require_once "includes/helpers.php";

const SEARCH = 'SEARCH';
const INSERT = 'INSERT';
const UPDATE = 'UPDATE';
const DELETE = 'DELETE';

$option = (isset($_POST['submitted']) ? $_POST['submitted'] : null);

if(isset($_POST['refresh'])){
    $_SESSION['refresh'] = true;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if(!isset($_POST['refresh'])){
    $_SESSION['refresh'] = false;
}

if ($option != null){
    switch($option){
        case SEARCH:
            if(empty($_POST['search'])){
                echo '<div id="err">Search field is empty, try again</div>' . "\n";
            } else{
                $result = search($_POST['search']);
                if($result === 0){
                    echo '<div id="err">Search found nothing</div>' . "\n";
                }
            }
            break;
        case INSERT:
            if(empty($_POST['website_name']) || empty($_POST['website_url']) || empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])){
                echo '<div id="err">There is an empty field</div>' . "\n";
            } else{
                $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
                insert(
                    $_POST['website_name'],
                    $_POST['website_url'],
                    $_POST['username'],
                    $_POST['email'],
                    $_POST['password'],
                    $comment
                );
            }
            break;
        case UPDATE:
            if(empty($_POST['new-attribute']) || empty($_POST['pattern'])){
                echo '<div id="err">A field is empty</div>' . "\n";
            } else{
                update($_POST['table'], $_POST['current-attribute'], $_POST['new-attribute'], $_POST['query-attribute'], $_POST['pattern']);
            }
            break;
        case DELETE:
            if(empty($_POST['website_name']) || empty($_POST['username'])){
                echo '<div id="err">There is an error with your request, try again</div>' . "\n";
            } else{
                $deleted = delete($_POST['website_name'], $_POST['username']);
            }
            break;
    }
}
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <button type="submit" name="refresh">Refresh Button</button>
</form>


<section>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <fieldset>
            <legend>Search</legend>
            <input type="text" name="search" autofocus required>
            <input type="hidden" name="submitted" value="SEARCH">
            <p><input type="submit" value="Search"></p>
        </fieldset>
    </form>
</section>

<section>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <fieldset>
            <legend>Insert</legend>
            <label for="website_name">Website Name: </label>
            <input type="text" name="website_name" placeholder="Website Name" required>

            <label for="website_url">Website URL: </label>
            <input type="text" name="website_url" placeholder="Website URL" required>

            <label for="username">Username: </label>
            <input type="text" name="username" placeholder="Username" required>

            <label for="email">Email Address: </label>
            <input type="text" name="email" placeholder="Email address" required>

            <label for="password">Password: </label>
            <input type="text" name="password" placeholder="password" required>

            <label for="comment">Comment: </label>
            <input name="text" placeholder="comment"></textarea>

            <input type="hidden" name="submitted" Value="INSERT">
            <p><input type="submit" value="Insert"></p>
        </fieldset>
    </form>
</section>

<section>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <fieldset>
            <legend>Update Website</legend>
            Update this table:
            <select name="table" id="table">
                <option value="websites">
                Websites
                </option>
            </select>
            Choose attribute to update:
            <select name="current-attribute" id="current-attribute">
                <option value="website_name">
                Website name
                </option>
                <option value="website_url">
                Website url
                </option>
            </select>
            Name of new website: <input type="text" name="new-attribute" placeholder="New website name" required>

            Choose old attribute to update: <select name="query-attribute" id="query-attribute">
                <option value="website_name">
                website name
                </option>
                <option value="website_url">
                Website url
                </option>
            </select>
            Name of old website: <input type="text" name="pattern" placeholder="Current Website name" required>
            <input type="hidden" name="submitted" value="UPDATE">
            <p><input type="submit" value="Update"></p>
        </fieldset>
    </form>

    <form method="post" action="<?PHP echo $_SERVER['PHP_SELF']; ?>">
        <fieldset>
            <legend>Update account</legend>
            Update this Table:
            <select name="table">
                <option value="accounts">
                    Accounts
                </option>
            </select>

            Select attribute to update: <select name="current-attribute" id="current-attribute">
                <option value="username">
                    Username
                </option>
                <option value="email">
                    Email
                </option>
                <option value="password">
                    Password
                </option>
                <option value="comment">
                    Comment
                </option>
            </select>
            New attribute: <input type="text" name="new-attribute" placeholder="New attribute to update" required>
            Choose old attribute to update: <select name="query-attribute" id="query-attribute">
            <option value="username">
                    Username
                </option>
                <option value="email">
                    Email
                </option>
                <option value="password">
                    Password
                </option>
                <option value="comment">
                    Comment
                </option>
            </select>
            Is called: <input type="text" name="new-attribute" placeholder="Old attribute name" required>
            <input type="hidden" name="submitted" value="UPDATE">
            <p><input type="submit" value="Update"></p>
        </fieldset>
    </form>
</section>

<section>
    <form method="post" action="<?PHP echo $_SERVER['PHP_SELF']; ?>">
        <fieldset>
            <legend>Delete</legend>
            <label for="website_name">Website Name: </label>
            <input type="text" name="website_name" placeholder="Website Name" required>

            <label for="username">Username: </label>
            <input type="text" name="username" placeholder="Username" required>

            <input type="hidden" name="submitted" value="DELETE">
            <p><input type="submit" value="Delete"></p>
        </fieldset>
    </form>
</section>
</body>
</html>
