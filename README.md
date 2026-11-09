# Emails Log extension for Magento 2
The Emails Log extension for Magento 2 / Adobe Commerce is a powerful debugging tool that allows you to analyze sent emails. Administrators and owners of online stores can use it to see the sending status, content of emails and other necessary and important information.

The report appears in a separate grid and contains the following data for all the emails sent from your Magento 2 store:
- recipient
- Bcc
- subject of the email
- status of the email: Success, Failed or Not Sent
- template the email was built from
- store view the email was sent for
- created at
- action - a link to the detailed view of the email

You can also export the report for further analysis.

Key features include:
- Comprehensive report
- Clickable column actions
- Built-in export to CSV or Excel XML
- Option to clear records older than (N days)
- Optional logging of emails that the store is configured not to send
- Email content rendered in an isolated frame, so it cannot affect the Admin panel
- Access to the log protected by its own permission resource

All columns of the report can be sorted, and almost all can be filtered. Moreover, the report can be exported for further import and analysis. You can export the report to CSV or Excel XML. You can also set up an automatic cleanup of log entries after the number of days you specify.

Refer to the extension page for the full description and user guide: https://commercemarketplace.adobe.com/freento-emailslog.html

## Logging while email communications are disabled
When `Stores > Configuration > Advanced > System > Mail Sending Settings > Disable Email Communications` is set to `Yes`, Magento sends no emails at all. Set `Log emails even if email communications is disabled` to `Yes` in the extension configuration, and such emails are still written to the log with the `Not Sent` status, although they are not delivered. This is useful on staging and QA environments, where outgoing mail must stay off, but you need to see which emails the store would have sent.

Sales emails (order, invoice, shipment, credit memo and their comment emails) are an exception: Magento skips them before they are composed, so they are not logged in this mode. The setting never affects email delivery itself, it only controls what is written to the log.

## Requirements
- PHP 8.1 or later
- Freento_Base, installed automatically as a Composer dependency

## Installation
You can install the Emails Log extension with Composer:
```
composer require freento/emailslog
```
Then run:
```
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
```

## Configuration and permissions
The extension settings are located in `Stores > Configuration > Freento > Emails Log`, and the grid itself in `Freento > Emails Log` in the Admin menu.

Access to the log is controlled by the `Freento > Emails Log` resource. Grant it in `System > Permissions > User Roles` to every role that needs access to the log, otherwise the grid and the view page are not available to that role.
