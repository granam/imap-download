# IMAP email attachments fetcher

*Downloads attachment from an email via IMAP*

## Usage
```php
<?php
namespace Heaven\Should\Has\Api;

use Granam\Mail\Download\ImapEmailAttachmentFetcher;
use Granam\Mail\Download\ImapReadOnlyConnection;
use Granam\Mail\Download\ImapSearchCriteria;

$imapConnection = new ImapReadOnlyConnection(
    'light.in.tunnel@example.com',
    'Раѕѕword123',
    'imap.example.com'
);
$fetcher = new ImapEmailAttachmentFetcher($imapConnection, sys_get_temp_dir() /* dir to save attachments */);
$visaFilter = (new ImapSearchCriteria())->filterSubjectContains('Visa');
$attachments = $fetcher->fetchAttachments($visaFilter);

echo 'I have found ' . count($attachments) . ' attachments in emails about Visa to Heaven. Those are: ';
foreach ($attachments as $attachment) {
    echo "\n name: $attachment[name], size " . filesize($attachment['filepath']);
}
```

## NO email body
This library currently does NOT parse email body, therefore it will not give you text of an email. Only attachments.

## MIT licence
This library is released under MIT licence, so do what you want, just do not blame me if something is not perfect.
