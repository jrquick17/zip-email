<!DOCTYPE html>
<html>
<head>
    <title>Zip Email</title>

    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.js"></script>
    <link href="node_modules/bootstrap/dist/css/bootstrap.css" rel="stylesheet"/>

    <script src="lib/analytics.js"></script>

    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({
            google_ad_client:      "ca-pub-3589546292454760",
            enable_page_level_ads: true
        });
    </script>

    <link href="styles.css" rel="stylesheet"/>
</head>
<body>
    <?php if (isset($_GET['message'])) { ?>
        <div class="alert alert-danger"
             role="alert">
            <?= $_GET['message'] ?>
        </div>
    <? } ?>

    <form class="row zip-form"
          novalidate
          action="/convert.php"
          method="POST">
        <div class="col-sm-12 col-md-6 form-group">
            <label for="username">
                Email
            </label>
            <input class="form-control"
                   id="username"
                   name="username"
                   type="text"
                   placeholder="jrquick"
                   required/>
<!--                <div class="input-group-append">-->
<!--                    <div class="input-group-text">@AOL.COM</div>-->
<!--                </div>-->
            <div class="invalid-feedback">
                Please enter an email address.
            </div>
        </div>

        <div class="col-sm-12 col-md-6 form-group">
            <label for="password">
                Password
            </label>
            <input class="form-control"
                   id="password"
                   name="password"
                   type="password"
                   placeholder="********"
                   required/>
            <div class="invalid-feedback">
                Please enter a password.
            </div>
        </div>

        <div class="col-12 form-group">
            <label for="folder">
                FOLDER
            </label>
            <input class="form-control"
                   id="folder"
                   name="folder"
                   type="text"
                   placeholder="Inbox"/>
        </div>

        <div class="col-12">
            <small class="form-text text-muted">
                No worries, your information is not shared nor stored in any way.
            </small>
        </div>

        <div class="col-12">
            <input class="btn btn-primary zip-button"
                   type="submit"
                   value="ZIP EMAILS"/>
        </div>
    </form>

    <script>
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('zip-form');

                Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }

                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html>