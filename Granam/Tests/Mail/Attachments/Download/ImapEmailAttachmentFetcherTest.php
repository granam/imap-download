<?php
namespace Granam\Tests\Mail\Attachments\Download;

use Granam\Mail\Attachments\Download\ImapEmailAttachmentFetcher;
use Granam\Mail\Attachments\Download\ImapReadOnlyConnection;
use Granam\Mail\Attachments\Download\ImapSearchCriteria;
use PHPUnit\Framework\TestCase;

class ImapEmailAttachmentFetcherTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_fetch_attachment_from_email_via_imap()
    {
        $imapEmailAttachmentFetcher = new ImapEmailAttachmentFetcher($this->getImapReadOnlyConnection(), sys_get_temp_dir());
        $attachments = $imapEmailAttachmentFetcher->fetchAttachments((new ImapSearchCriteria())->fetchAll());
        self::assertCount(1, $attachments);
    }

    private function getImapReadOnlyConnection(): ImapReadOnlyConnection
    {
        return new ImapReadOnlyConnection(
            'test.imap.attachments@gmail.com',
            'Djw73FkgFy4afctepzkM',
            'imap.gmail.com'
        );
    }
}
