## Introduction

This MODX Extra adds menu entry in the user menu that allows you to select a
MODX user and log in as that user in the frontend without a password. If you are
logged as manager user, you can also use the password `loginas` to log into a
web user account in the frontend.

LoginAs uses the following system settings in the namespace `loginas`:

| Key                   | Name                | Description                                                              | Default |
|-----------------------|---------------------|--------------------------------------------------------------------------|---------|
| loginas.add_contexts  | Additional Contexts | Comma-separated list of additional contexts for frontend authentication. | -       |
| loginas.debug         | Debug               | Log debug information in the MODX error log.                             | No      |
| loginas.login_context | Login Context       | Main context for frontend authentication.                                | web     |
| loginas.login_id      | Login ID            | ID of a MODX Resource with a Login snippet call.                         | -       |

### Contexts with different (sub-)domains

If you need to log in to a context with a different domain than your current 
manager one, first log in to your manager account via this domain.

Example: The frontend for the context `login` uses the subdomain 
`login.domain.com`. Therefore, first log in to the manager via 
`login.domain.com/manager` first, and then use the LoginAs feature.

### Permissions

LoginAs can be restricted by permissions. The following permissions are 
available:

| Permission      | Description                                                                   |
|-----------------|-------------------------------------------------------------------------------|
| loginas_loginas | Permission to login as manager user to a frontend account without a password. |

The permission checks are not executed for sudo users.
