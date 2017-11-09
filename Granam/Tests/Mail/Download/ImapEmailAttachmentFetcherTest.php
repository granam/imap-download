<?php
namespace Granam\Tests\Mail\Download;

use Granam\Mail\Download\ImapEmailAttachmentFetcher;
use Granam\Mail\Download\ImapReadOnlyConnection;
use Granam\Mail\Download\ImapSearchCriteria;
use PHPUnit\Framework\TestCase;

class ImapEmailAttachmentFetcherTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_fetch_attachment_from_email_via_imap()
    {
        $imapEmailAttachmentFetcher = new ImapEmailAttachmentFetcher($this->getImapReadOnlyConnection(), sys_get_temp_dir());
        $attachments = $imapEmailAttachmentFetcher->fetchAttachments(
            (new ImapSearchCriteria())->filterSubjectContains('Aplikace OMS - data file 09.11.2017')
        );
        self::assertCount(1, $attachments);
        $attachment = current($attachments);
        self::assertSame('Vzor FLAT.txt', $attachment['name']);
        self::assertSame('Vzor FLAT.txt', $attachment['original_filename']);
        self::assertFileExists($attachment['filepath']);
        self::assertSame(
            file_get_contents(__DIR__ . '/data/Vzor FLAT.txt'),
            file_get_contents($attachment['filepath'])
        );
        unlink($attachment['filepath']);
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
