<!DOCTYPE html>
<html>
<head>
  <title>Zip Email</title>

  <script src="node_modules/jquery/dist/jquery.js"></script>

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

  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-75605274-10"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-75605274-10');
  </script>

  <link href="styles.css" rel="stylesheet"/>
</head>
<body>
    <div id="error-alert"
         class="alert alert-danger"
         role="alert">
        No emails found.
    </div>

    <div id="loading-animation"
         class="modal hide"
         tabindex="-1"
         role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body text-center modal-text">
                    <p>Loading...</p>
                </div>
            </div>
        </div>
    </div>

    <form class="row zip-form"
          novalidate
          action="convert.php?action=getEmails"
          target="_blank"
          method="POST">
        <div id="provider-select-input"
             class="form-group col-12">
            <label for="provider-select">
                Email Provider
            </label>
            <select id="provider-select"
                    name="provider"
                    class="form-control">
                <option value="aol">AOL</option>
                <option value="gmail">GMail</option>
                <option value="outlook">Outlook</option>
                <option value="yahoo">Yahoo</option>
                <option value="custom">Custom</option>
            </select>
        </div>

        <div class="col-sm-12 col-md-6 form-group">
            <label for="username">
                Email
            </label>
            <input class="form-control"
                   id="username"
                   name="username"
                   type="text"
                   placeholder="jrquick"
                   value="<?= $_GET['username'] ?>"
                   required/>
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
                   value="<?= $_GET['password'] ?>"
                   required/>
            <div class="invalid-feedback">
                Please enter a password.
            </div>
        </div>

        <div id="folder-text-input"
             class="col-12">
            <label for="folder-text">
                Folder Name
            </label>
            <div class="input-group">
                <input class="form-control"
                       id="folder-text"
                       name="folder"
                       type="text"
                       placeholder="Inbox"/>
                <div class="input-group-append">
                    <button id="load-folder-button"
                            class="btn btn-outline-secondary"
                            type="button">
                        Load Folder Options
                    </button>
                </div>
            </div>
        </div>

        <div id="folder-select-input"
             class="form-group col-12">
            <label for="folder-select">
                Select A Folder...
            </label>
            <select id="folder-select"
                    class="form-control">
            </select>
        </div>

        <div class="buttons col-12">
            <input id="zip-button"
                   class="btn btn-primary zip-button"
                   type="submit"
                   value="ZIP EMAILS"/>
            <small class="form-text text-muted text-right">
                *No worries, your information is not shared nor stored in any way.
            </small>
        </div>
    </form>

    <script type="text/javascript">
        (function() {
            'use strict';
            window.addEventListener(
                'load',
                function() {
                    var displayErrors = function(response) {
                        var element = $('#error-alert');

                        element.hide();
                        element.text(response.errors);
                        element.show();
                    };

                    var beginLoading = function() {
                        $('#error-alert').hide();

                        showLoading();

                        $('#zip-button').prop('disabled', true);
                    };

                    var endLoading = function() {
                        hideLoading();

                        $('#zip-button').prop('disabled', false);
                    };

                    var hideLoading = function() {
                        $('.modal-backdrop').hide();
                        $('#loading-animation').hide();
                    };

                    var showLoading = function() {
                        $('.modal-backdrop').show();
                        $('#loading-animation').show();
                    };

                    var revertFolderSelect = function(error) {
                        var response = {
                            errors: [
                                error
                            ]
                        };

                        displayErrors(response);

                        $('#folder-text-input').show();
                    };

                    $('#folder-select-input').hide();
                    $('#error-alert').hide();

                    $('#loading-animation').modal({
                        backdrop: 'static',
                        focus:    true,
                        keyboard: false,
                        show:     true,
                    });
                    hideLoading();

                    $('#folder-select').change(
                        function() {
                            $('#folder-text')[0].value = $('#folder-select option:selected')[0].value;
                        }
                    );

                    $('#load-folder-button').click(
                        function() {
                            beginLoading();

                            $('#folder-text-input').hide();

                            var data = {
                                provider: $('#provider-select option').filter(':selected').val(),
                                username: $('#username')[0].value,
                                password: $('#password')[0].value
                            };

                            $.ajax({
                                data:    data,
                                url:     'convert.php?action=getFolderOptions',
                                context: document.body,
                                type:    'POST'
                            }).done(
                                function(response) {
                                    try {
                                        response = JSON.parse(response);

                                        if (typeof response.folders !== 'undefined' && typeof response.folders.length !== 'undefined') {
                                            var items = response.folders;
                                            if (items.length === 0) {
                                                revertFolderSelect('No folders found.');
                                            } else {
                                                $('#folder-select option').remove();

                                                var folderSelect = $('#folder-select');

                                                folderSelect.append(
                                                    $('<option>', {})
                                                );

                                                $.each(
                                                    items,
                                                    function(i, item) {
                                                        folderSelect.append(
                                                            $(
                                                                '<option>',
                                                                {
                                                                    value: item,
                                                                    text:  item
                                                                }
                                                            )
                                                        );
                                                    }
                                                );

                                                $('#folder-select-input').show();
                                            }
                                        } else {
                                            if (typeof response.errors !== 'undefined') {
                                                displayErrors(response);
                                            }

                                            $('#folder-text-input').show();
                                        }
                                    } catch(e) {
                                        revertFolderSelect('Invalid response returned.');
                                    }

                                    endLoading();
                                }
                            ).fail(
                                function() {
                                    revertFolderSelect('An unknown error occurred.');

                                    endLoading();
                                }
                            );
                        }
                    );

                    $('.zip-form').submit(
                        function() {
                            if (this.checkValidity() === false) {
                                event.preventDefault();
                                event.stopPropagation();
                            }

                            this.classList.add('was-validated');
                        }
                    );
                }, false);
            }
        )();
    </script>
</body>
</html>
