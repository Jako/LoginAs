## Introduction

This MODX Extra adds menu entry in the user menu that allows you to select a
MODX user and log in as that user in the frontend without a password. If you are
logged as manager user, you can also use the password `loginas` to log into a
web user account in the frontend.

LoginAs uses the following system settings in the namespace `loginas`:

| Key                   | Name                | Description                                                              | Default |
|-----------------------|---------------------|--------------------------------------------------------------------------|---------|
| loginas.add_contexts  | Additional Contexts | Comma separated list of additional contexts for frontend authentication. | -       |
| loginas.debug         | Debug               | Log debug information in the MODX error log.                             | No      |
| loginas.login_context | Login Context       | Main context for frontend authentication.                                | web     |
| loginas.login_id      | Login ID            | ID of a MODX Resource with a Login snippet call.                         | -       |

### Context with different (sub-)domains

If you want to log in to a context that uses a different domain to the current manager, log in to the manager via this subdomain first.

Example: The front end for the context `login` uses the subdomain `login.domain.com`. Therefore, log in to the Manager via `login.domain.com/manager` first, and then use the LoginAs feature.

### Permissions

To work with the component the user must have appropriate rights:

- loginas_loginas - Permission to login as manager user to a frontend account without a password.

A sudo user is allowed to do everything.
