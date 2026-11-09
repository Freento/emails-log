# Release Notes

## 1.4.0

This release adds logging of emails that Magento does not send, closes a security issue on
the email view page, makes logging unable to break email delivery, and moves the module under
the shared Freento admin menu.

### Requirements

- **Freento_Base is now required.** It is declared as a Composer dependency (`freento/base`) and
  in `etc/module.xml`, so Composer installs it automatically together with this module — no manual
  installation needed. The module relies on Freento_Base for the top level "Freento" admin menu
  item, the `Freento_Base::freento_base` ACL resource and the "freento" configuration tab.

### New features

- **New setting: "Log emails even if email communications is disabled"**
  (`Stores > Configuration > Freento > Emails Log`, `emailslog/general/log_when_emails_disabled`,
  default "No"). It only applies when the core setting `Stores > Configuration > Advanced >
  System > Mail Sending Settings > Disable Email Communications` is set to "Yes":
  - **Yes** — emails are still written to the log, with the new "Not Sent" status, although they
    are not delivered. Useful on staging and QA environments where outgoing mail must stay off,
    but you still need to see which emails the store would have sent.
  - **No** — nothing is logged while email communications are disabled (the 1.3.2 behaviour).

  When "Disable Email Communications" is "No", the setting has no effect: emails are sent and
  logged as usual. The setting never affects email delivery itself, it only controls the log.

  **Known limitation:** sales emails (order, invoice, shipment, credit memo and their comment
  emails) are not logged even when the setting is "Yes". While email communications are disabled,
  Magento skips these emails before they are composed, so the module has nothing to intercept.

- **New "Not Sent" log status** for emails that were logged but not delivered because email
  communications are disabled. It is available as a filter in the Emails Log grid.

- **Emails sent from the Admin panel now show their store.** Store id `0` (Admin) is stored and
  rendered as "Admin" instead of "-", both in the grid and on the view page. This affects new
  records only; existing records keep an empty store.

### Security

- **The email body is now always rendered in an isolated iframe.** The email HTML is no longer
  inserted directly into the Admin page, so it can neither run scripts with the permissions of the
  current admin user, nor override the styles of the Admin panel.
- **The iframe response is hardened.** `Controller\Adminhtml\Log\Iframe` now sends
  `Content-Security-Policy: sandbox allow-same-origin allow-popups allow-popups-to-escape-sandbox`,
  `X-Content-Type-Options: nosniff` and an explicit `Content-Type`. The same flags are applied to
  the `sandbox` attribute of the iframe. Scripts and forms inside the email are blocked, also when
  the iframe URL is opened directly.
- **All Admin controllers now declare an ACL resource.** `Index`, `View`, `Iframe` and `MassDelete`
  use `ADMIN_RESOURCE = 'Freento_EmailsLog::freento_log'`. Previously they fell back to the default
  `Magento_Backend::admin`, so email logs, including password reset links, were available to any
  admin user. The menu item now points to this existing resource as well, instead of an ACL
  resource that was never declared.

  **Action required:** grant the "Freento > Emails Log" resource to the roles that need access.

### Fixes

- **A logging failure can no longer break email delivery.** Writing the log was moved out of the
  `finally` block into a dedicated method that never throws: any failure (for example a database
  error) is written to `exception.log` instead. Sending errors are caught as `Throwable`, so an
  email that fails with a PHP error is now logged as "Failed" too, and the original exception is
  passed through unchanged.
- **Emails with no "To" address and emails with no subject no longer cause a fatal error.**
- **All "To" recipients are logged**, comma separated, instead of the first one only.
- **The view page no longer fails for emails without a store**, for example the Admin
  "Forgot Password" email.
- **Fixed the layout of the view page.** The columns use flex instead of floats, and long
  unbreakable strings (such as password reset links) no longer push the content column below the
  information column.
- **Fixed an invalid DI preference**.
- **API and constructor signatures changed** in the repository interfaces, `Helper\Config`, the
  view block and the transport plugin. Third party code extending these classes may need updates.

### Removed

- **The "Display content in a frame" setting** (`emailslog/general/show_iframe`) was removed, as
  the email body is now always shown in an iframe. A leftover `emailslog/general/show_iframe` row
  may remain in `core_config_data`; it is no longer used.
- **The module no longer declares the "freento" configuration tab** — it is declared by
  Freento_Base now.
- **The module no longer creates its own root admin menu item** under System. "Emails Log" moved
  under the "Freento" menu item provided by Freento_Base.

### Upgrade notes

1. Update the module with Composer. Freento_Base is pulled in as a dependency, so no separate
   installation step is needed.
2. Grant the "Freento > Emails Log" ACL resource to the roles that need access to the log.
