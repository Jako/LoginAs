## Introduction

LoginAs adds the entry **Log in as** to the user menu of the MODX manager. It
opens a window, where you select a MODX user and log in as that user without a
password, either in the frontend or in the manager.

In addition, you can log into a frontend account with the password `loginas`
in a frontend login form, if you are logged into the manager in the same
browser.

## Login as web user

Open the user menu in the manager and click on **Log in as**. Select a user in
the **Username** field and click on **Log in as**. You will be logged into the
frontend contexts set in the system settings `loginas.login_context` and
`loginas.add_contexts` as the selected user. After that, you will be redirected
to the resource set in `loginas.login_id`, or to the site start, if that
setting is empty.

Your manager session is not changed by this login. The user list only contains
active web users. Users with manager access and sudo users are not listed.

## Login as manager user

With the `loginas_loginas_manager` permission, the LoginAs window shows the
additional option **Log into the manager**. If it is enabled, the user list
only contains active users with manager access, except your own user.

After a click on **Log in as**, you have to confirm that you will be logged out
as the current manager user. Then you will be logged into the manager as the
selected user. To continue as your own user, you have to log out and log in
again with your password.

The following rules apply to a login into the manager:

* The selected user needs access to the manager (the `frames` permission).
* You can't log in as your own user.
* The `OnBeforeManagerLogin` event is not fired for this login. So plugins on
  this event, i.e. two-factor authentication plugins, don't ask for a code of
  the selected user.

## Login with the password `loginas`

If you are logged into the manager, you can use the password `loginas` in any
frontend login form, i.e. the form of the Login snippet, to log into a
frontend account of another user. This only works, if

* your manager user has the `loginas_loginas` permission in the `mgr`
  context,
* the login form does not log into the `mgr` context.

## Restrictions

* Only sudo users can log in as another sudo user. For other users, sudo
  users are not listed.
* Inactive users are not listed. Inactive or blocked users can't be logged in.
* A login into the frontend never adds the `mgr` context, even if it is set in
  `loginas.login_context` or `loginas.add_contexts`.
* Each login with the LoginAs window is logged in the manager log of the
  current manager user (actions `loginas_login_as` and
  `loginas_login_as_manager`).

## System Settings

LoginAs uses the following system settings in the namespace `loginas`:

| Key                   | Name                | Description                                                                                                          | Default |
|-----------------------|---------------------|----------------------------------------------------------------------------------------------------------------------|---------|
| loginas.add_contexts  | Additional Contexts | Comma-separated list of additional contexts for frontend authentication.                                             | -       |
| loginas.debug         | Debug               | Load the unminified manager javascript source files.                                                                 | No      |
| loginas.login_context | Login Context       | Main context for frontend authentication.                                                                            | web     |
| loginas.login_id      | Login ID            | ID of a MODX resource where you are redirected to after a login into the frontend. If empty, the site start is used. | 0       |

### Contexts with different (sub-)domains

If you need to log in to a context with a different domain than your current
manager one, first log in to your manager account via this domain.

Example: The frontend for the context `login` uses the subdomain
`login.domain.com`. Therefore, first log in to the manager via
`login.domain.com/manager` and then use the LoginAs feature.

## Permissions

LoginAs can be restricted by permissions. The following permissions are
available:

| Permission              | Description                                                                                                                    |
|-------------------------|--------------------------------------------------------------------------------------------------------------------------------|
| loginas_loginas         | Permission to login as manager user to a frontend account without a password.                                                  |
| loginas_loginas_manager | Permission to login as manager user to another manager account without a password. Needs the `loginas_loginas` permission too. |

The installation creates two access policies:

| Access Policy   | Permissions                                          |
|-----------------|------------------------------------------------------|
| LoginAs         | `loginas_loginas`                                    |
| LoginAs Manager | `loginas_loginas` and `loginas_loginas_manager`      |

To grant the permissions, add a context access to the `mgr` context with one of
these access policies to the user group of the manager users. An upgrade does
not change the permissions of an existing access policy, so the `LoginAs`
policy of an older version does not get the `loginas_loginas_manager`
permission.

!!! warning

    A user with the `loginas_loginas_manager` permission can log into the
    manager as each non-sudo manager user, including administrators that are
    not sudo users. Grant this permission only to users you trust with these
    accounts.

The permission checks are not executed for sudo users.
