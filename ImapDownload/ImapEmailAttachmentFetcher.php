<?php declare(strict_types=1);

namespace Granam\Mail\Download;

use Granam\Strict\Object\StrictObject;

/** @link https://stackoverflow.com/questions/2649579/downloading-attachments-to-directory-with-imap-in-php-randomly-works */
class ImapEmailAttachmentFetcher extends StrictObject
{

    /** @var ImapReadOnlyConnection */
    private $imapReadOnlyConnection;
    /** @var string */
    private $dirToSave;

    /**
     * @param ImapReadOnlyConnection $imapReadOnlyConnection
     * @param string $dirToSave Temp dir is recommended
     */
    public function __construct(ImapReadOnlyConnection $imapReadOnlyConnection, string $dirToSave = '')
    {
        $this->imapReadOnlyConnection = $imapReadOnlyConnection;
        $this->dirToSave = rtrim($dirToSave !== '' ? $dirToSave : sys_get_temp_dir(), '\\/');
    }

    /**
     * @param ImapSearchCriteria $imapSearchCriteria
     * @return array|string[][] List of files [filepath => ..., original_filename => ..., name => ...]
     * @throws \Granam\Mail\Download\Exceptions\UnknownSearchCriteria
     * @throws \RuntimeException
     */
    public function fetchAttachments(ImapSearchCriteria $imapSearchCriteria): array
    {
        $inbox = $this->imapReadOnlyConnection->getResource();
        $emailNumbers = imap_search($inbox, $imapSearchCriteria->getAsString(), SE_FREE, $imapSearchCriteria->getCharsetForSearch());
        if ($emailNumbers === false) {
            if (imap_last_error() !== false) {
                throw new Exceptions\UnknownSearchCriteria(
                    "IMAP probably does not recognize filter '{$imapSearchCriteria->getAsString()}'"
                    . '; ' . imap_last_error()
                );
            }

            return [];
        }
        if (count($emailNumbers) === 0) {
            $this->imapReadOnlyConnection->closeResource();

            return [];
        }
        $attachmentFiles = [];
        foreach ($emailNumbers as $messageNumber) {
            $structure = imap_fetchstructure($inbox, $messageNumber);
            $attachments = [];
            if (!empty($structure->parts)) {
                foreach ($structure->parts as $index => $part) {
                    $attachment = $this->collectAttachment($part, $inbox, $messageNumber, $index + 1);
                    if ($attachment) {
                        $attachments[] = $attachment;
                    }
                }
            }
            foreach ($attachments as $attachment) {
                if ($attachment['is_attachment']) {
                    $attachmentFiles[] = [
                        'filepath' => $this->writeAttachment($attachment['attachment']),
                        'original_filename' => $attachment['filename'],
                        'name' => $attachment['name'],
                    ];
                }
            }
        }
        $this->imapReadOnlyConnection->closeResource();

        return $attachmentFiles;
    }

    /**
     * @param object $part
     * @param $inbox
     * @param $messageNumber
     * @param int $section
     * @return array|null
     */
    private function collectAttachment(object $part, $inbox, $messageNumber, int $section): ?array
    {
        $attachment = [
            'is_attachment' => false,
            'filename' => '',
            'name' => '',
            'attachment' => '',
        ];
        if ($part->ifdparameters) { // TRUE if the dparameters array exists
            foreach ($part->dparameters as $object) {
                if (strtolower($object->attribute) === 'filename') {
                    $attachment['is_attachment'] = true;
                    $attachment['filename'] = $object->value;
                }
            }
        }
        if ($part->ifparameters) { // TRUE if the parameters array exists
            foreach ($part->parameters as $object) {
                if (strtolower($object->attribute) === 'name') {
                    $attachment['is_attachment'] = true;
                    $attachment['name'] = $object->value;
                }
            }
        }

        if ($attachment['is_attachment']) {
            $attachment['attachment'] = imap_fetchbody($inbox, $messageNumber, (string)$section);
            if ((int)$part->encoding === ENCBASE64) {
                $attachment['attachment'] = base64_decode($attachment['attachment']);
            } elseif ((int)$part->encoding === ENCQUOTEDPRINTABLE) {
                $attachment['attachment'] = quoted_printable_decode($attachment['attachment']);
            }
        }

        if ($attachment['is_attachment']) {
            return $attachment;
        }

        return null;
    }

    private function writeAttachment(string $attachment): string
    {
        if (!file_exists($this->dirToSave) && !@mkdir($this->dirToSave, 0770, true) && !is_dir($this->dirToSave)) {
            throw new \RuntimeException('Could not create dir to save email attachments: ' . $this->dirToSave);
        }
        $filename = (uniqid('imap', true) . '.attachment');
        $fullFilename = $this->dirToSave . '/' . $filename;
        $handle = @fopen($fullFilename, 'wb');
        if (!$handle) {
            throw new \RuntimeException(
                'Could not save an email attachment as ' . $fullFilename . '; ' . var_export(error_get_last(), true)
            );
        }
        if (@fwrite($handle, $attachment) === false) {
            fclose($handle);
            unlink($fullFilename);
            throw new \RuntimeException(
                'Could not write an email attachment into ' . $fullFilename . '; ' . var_export(error_get_last(), true)
            );
        }
        fclose($handle);

        return $fullFilename;
    }

}
