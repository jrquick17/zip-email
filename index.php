<form action="/convert.php"
      method="POST">
    <label for="username">
        USERNAME
    </label>
    <input id="username"
           name="username"
           type="text"
           value="email@aol.com"/>

    <label for="password">
        PASSWORD
    </label>
    <input id="password"
           name="password"
           type="text"
           value="password"/>

    <label for="folder">
        FOLDER
    </label>
    <input id="folder"
           name="folder"
           type="text"
           value="Inbox"/>

    <input type="submit"
           value="ZIP EMAILS"/>
</form>

<div style="color: red;">
    <p><?= $_GET['message'] ?></p>
</div>