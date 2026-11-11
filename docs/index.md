# LoginAs

Log in as another MODX user in the frontend or in the manager without a
password. This MODX Extra adds an entry to the user menu of the manager that
allows you to select a MODX user and log in as that user. If you are logged into
the manager, you can also use the password `loginas` to log into a web user
account in a frontend login form.

### Requirements

* MODX Revolution 2.8+
* PHP 7.4+

### Features

* Menu entry in the user menu of the manager that allows you to select a MODX
  web user and log in as that user in the frontend without a password.
* Optionally log into the manager as another manager user, after a
  confirmation that you will be logged out as the current manager user.
* Log in as user in a frontend login form with the password `loginas`.
* The features are restricted by the `loginas_loginas` and
  `loginas_loginas_manager` permissions. Only sudo users can log in as another
  sudo user.
* Each login with the menu entry is logged in the manager log.

### License

The project is licensed under the [GPLv2 license](https://github.com/Jako/LoginAs/LICENSE.md).

### Translations [![Default Lexicon](https://hosted.weblate.org/widget/modx-extras/loginas/svg-badge.svg)](https://hosted.weblate.org/projects/modx-extras/loginas/)

Translations of the package can be made for the [Default Lexicon](https://hosted.weblate.org/projects/modx-extras/loginas/standard/) and the [System Setting Lexicon](https://hosted.weblate.org/projects/modx-extras/loginas/system-settings/)
