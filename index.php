<form action="/convert.php"
      method="POST">
    <label for="username">
        USERNAME
    </label>
    <input id="username"
           name="username"
           type="text"
           value="jrquick628"/>

    <label for="password">
        PASSWORD
    </label>
    <input id="password"
           name="password"
           type="text"
           value="512mbddr2"/>

    <label for="folder">
        FOLDER
    </label>
    <input id="folder"
           name="folder"
           type="text"
           value=""/>

    <input type="submit"
           value="ZIP EMAILS"/>
</form>

<div style="color: red;">
    <p><?= $_GET['message'] ?></p>
</div>