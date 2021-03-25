<?php declare(strict_types=1);

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
    public function System_default_temp_dir_is_used_as_default_dir_for_attachments()
    {
        $imapEmailAttachmentFetcher = new ImapEmailAttachmentFetcher($this->getImapReadOnlyConnection());
        $reflection = new \ReflectionClass(ImapEmailAttachmentFetcher::class);
        $dirToSave = $reflection->getProperty('dirToSave');
        $dirToSave->setAccessible(true);
        self::assertSame(sys_get_temp_dir(), $dirToSave->getValue($imapEmailAttachmentFetcher));
    }

    /**
     * @test
     */
    public function I_can_fetch_attachment_from_email_via_imap()
    {
        $imapEmailAttachmentFetcher = new ImapEmailAttachmentFetcher($this->getImapReadOnlyConnection(), sys_get_temp_dir());
        $attachments = $imapEmailAttachmentFetcher->fetchAttachments(
            (new ImapSearchCriteria())->filterSubjectContains('Aplikace OMS - data file 22.04.2016')
        );
        self::assertCount(1, $attachments);
        $attachment = current($attachments);
        self::assertSame('VDAT-000819-123450001-123450001-20160422.TXT', $attachment['name']);
        self::assertSame('VDAT-000819-123450001-123450001-20160422.TXT', $attachment['original_filename']);
        self::assertFileExists($attachment['filepath']);
        self::assertFileEquals(
            __DIR__ . '/data/VDAT-000819-123450001-123450001-20160422.TXT',
            $attachment['filepath']
        );
        unlink($attachment['filepath']);
    }

    private function getImapReadOnlyConnection(): ImapReadOnlyConnection
    {
        return new ImapReadOnlyConnection(
            'test.imap.attachments@email.cz',
            'Djw73FkgFy4afctepzkM',
            'imap.seznam.cz'
        );
    }
}
