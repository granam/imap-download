<?php declare(strict_types=1);

namespace Granam\Mail\Download;

use Granam\Strict\Object\StrictObject;

/**
 * @link https://secure.php.net/manual/en/function.imap-search.php
 */
class ImapSearchCriteria extends StrictObject implements ToString
{
    /** @var string */
    private $charsetForSearch;

    /** @var bool */
    private $all = false;
    /** @var bool */
    private $answered = false;
    /** @var bool */
    private $unanswered = false;
    /** @var bool */
    private $important = false; // those marked as Flagged (Important or Urgent)
    /** @var bool */
    private $notImportant = false; // those not marked as Flagged
    /** @var bool */
    private $new = false;
    /** @var bool */
    private $oldOnly = false;
    /** @var bool */
    private $recent = false;
    /** @var bool */
    private $read = false;
    /** @var bool */
    private $unread = false;
    /** @var bool */
    private $deleted = false;
    /** @var bool */
    private $notDeleted = false;
    /** @var string */
    private $ccContains = '';
    /** @var string */
    private $bccContains = '';
    /** @var string */
    private $bodyContains = '';
    /** @var string */
    private $fromContains = '';
    /** @var string */
    private $subjectContains = '';
    /** @var string */
    private $textContains = '';
    /** @var string */
    private $toContains = '';
    /** @var string */
    private $keywordContains = '';
    /** @var string */
    private $keywordNotContains = '';
    /** @var \DateTimeInterface|null */
    private $byDate;
    /** @var \DateTimeInterface|null */
    private $before;
    /** @var \DateTimeInterface|null */
    private $since;

    /**
     * @param string $charsetForSearch @link https://secure.php.net/manual/en/mbstring.supported-encodings.php
     */
    public function __construct(string $charsetForSearch = 'UTF-8')
    {
        $this->charsetForSearch = $charsetForSearch;
    }

    public function getCharsetForSearch(): string
    {
        return $this->charsetForSearch;
    }

    /**
     * @return ImapSearchCriteria
     * @throws \Granam\Mail\Download\Exceptions\InvalidFilterCombination
     */
    public function fetchAll(): ImapSearchCriteria // - return all messages matching the rest of the criteria
    {
        if ($this->isAnswered() || $this->isUnanswered() || $this->isImportant() || $this->isNotImportant()
            || $this->isNew() || $this->isOldOnly() || $this->isRecent() || $this->isRead() || $this->isUnread()
            || $this->isDeleted() || $this->isNotDeleted() || $this->getCcContains() !== ''
            || $this->getBccContains() !== '' || $this->getBodyContains() !== ''
            || $this->getFromContains() || $this->getSubjectContains() || $this->getTextContains() !== ''
            || $this->getToContains() !== '' || $this->getKeywordContains() !== '' || $this->getKeywordNotContains() !== ''
            || $this->getByDate() !== null || $this->getBefore() !== null || $this->getSince() !== null
        ) {
            throw new Exceptions\InvalidFilterCombination(
                'Can not use ALL when there are already some specific search criteria: ' . $this->getAsString()
            );
        }
        $this->all = true;

        return $this;
    }

    public function filterAnswered(): ImapSearchCriteria // - match messages with the \\ANSWERED flag set
    {
        $this->answered = true;

        return $this;
    }

    /**
     * Filters emails with given string in Blind carbon copy (BCC) field (hidden copy)
     * @param string $stringInBccField
     * @return ImapSearchCriteria
     */
    public function setBccContains(string $stringInBccField): ImapSearchCriteria // "string" - match messages with "string" in the Bcc: field
    {
        $this->bccContains = $stringInBccField;

        return $this;
    }

    public function setBefore(\DateTimeInterface $messagesBefore): ImapSearchCriteria // "date" - match messages with Date: before "date"
    {
        $this->before = $messagesBefore;

        return $this;
    }

    public function setBodyContains(string $stringInBody): ImapSearchCriteria // "string" - match messages with "string" in the body of the message
    {
        $this->bodyContains = $stringInBody;

        return $this;
    }

    /**
     * Filters emails with given string in Carbon copy (CC) field (visible copy)
     * @param string $stringInCcField
     * @return ImapSearchCriteria
     */
    public function setCcContains(string $stringInCcField): ImapSearchCriteria // "string" - match messages with "string" in the Cc: field
    {
        $this->ccContains = $stringInCcField;

        return $this;
    }

    public function filterDeleted(): ImapSearchCriteria // - match deleted messages
    {
        $this->deleted = true;

        return $this;
    }

    /**
     * Filter only emails flagged as Important (sometimes Urgent)
     * @return static
     */
    public function filterFlagged(): ImapSearchCriteria // - match messages with the \\FLAGGED (sometimes referred to as Important or Urgent) flag set
    {
        $this->important = true;

        return $this;
    }

    public function setFromContains(string $stringInFrom): ImapSearchCriteria // "string" - match messages with "string" in the From: field
    {
        $this->fromContains = $stringInFrom;

        return $this;
    }

    public function filterKeywordContains(string $stringInKeyword): ImapSearchCriteria // "string" - match messages with "string" as a keyword
    {
        $this->keywordContains = $stringInKeyword;

        return $this;
    }

    public function filterNew(): ImapSearchCriteria // - match new messages
    {
        $this->new = true;

        return $this;
    }

    public function filterOld(): ImapSearchCriteria // - match old messages
    {
        $this->oldOnly = true;

        return $this;
    }

    public function filterByDate(\DateTimeInterface $dateTime): ImapSearchCriteria // "date" - match messages with Date: matching "date"
    {
        $this->byDate = $dateTime;

        return $this;
    }

    public function filterRecent(): ImapSearchCriteria // - match messages with the \\RECENT flag set
    {
        $this->recent = true;

        return $this;
    }

    public function filterRead(): ImapSearchCriteria // - match messages that have been read (the \\SEEN flag is set)
    {
        $this->read = true;

        return $this;
    }

    public function filterSince(\DateTimeInterface $since): ImapSearchCriteria // "date" - match messages with Date: after "date"
    {
        $this->since = $since;

        return $this;
    }

    public function filterSubjectContains(string $stringInSubject): ImapSearchCriteria // "string" - match messages with "string" in the Subject:
    {
        $this->subjectContains = $stringInSubject;

        return $this;
    }

    public function filterTextContains(string $stringInText): ImapSearchCriteria // "string" - match messages with text "string"
    {
        $this->textContains = $stringInText;

        return $this;
    }

    public function filterToContains(string $stringInToField): ImapSearchCriteria // "string" - match messages with "string" in the To:
    {
        $this->toContains = $stringInToField;

        return $this;
    }

    public function filterUnanswered(): ImapSearchCriteria // - match messages that have not been answered
    {
        $this->unanswered = true;

        return $this;
    }

    public function filterNotDeleted(): ImapSearchCriteria // - match messages that are not deleted
    {
        $this->notDeleted = true;

        return $this;
    }

    public function filterNotFlagged(): ImapSearchCriteria // - match messages that are not flagged
    {
        $this->notImportant = true;

        return $this;
    }

    public function filterKeywordNotContains(string $stringNotInKeyword): ImapSearchCriteria // "string" - match messages that do not have the keyword "string"
    {
        $this->keywordNotContains = $stringNotInKeyword;

        return $this;
    }

    public function filterNotRead(): ImapSearchCriteria // - match messages which have not been read yet
    {
        $this->unread = true;

        return $this;
    }

    public function isAll(): bool
    {
        return $this->all;
    }

    public function isAnswered(): bool
    {
        return $this->answered;
    }

    public function getBccContains(): string
    {
        return $this->bccContains;
    }

    public function getBefore(): ?\DateTimeInterface
    {
        return $this->before;
    }

    public function getBodyContains(): string
    {
        return $this->bodyContains;
    }

    public function getCcContains(): string
    {
        return $this->ccContains;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function isImportant(): bool
    {
        return $this->important;
    }

    public function getFromContains(): string
    {
        return $this->fromContains;
    }

    public function getKeywordContains(): string
    {
        return $this->keywordContains;
    }

    public function isNew(): bool
    {
        return $this->new;
    }

    public function isOldOnly(): bool
    {
        return $this->oldOnly;
    }

    public function getByDate(): ?\DateTimeInterface
    {
        return $this->byDate;
    }

    public function isRecent(): bool
    {
        return $this->recent;
    }

    public function isRead(): bool
    {
        return $this->read;
    }

    public function getSince(): ?\DateTimeInterface
    {
        return $this->since;
    }

    public function getSubjectContains(): string
    {
        return $this->subjectContains;
    }

    public function getTextContains(): string
    {
        return $this->textContains;
    }

    public function getToContains(): string
    {
        return $this->toContains;
    }

    public function isUnanswered(): bool
    {
        return $this->unanswered;
    }

    public function isNotDeleted(): bool
    {
        return $this->notDeleted;
    }

    public function isNotImportant(): bool
    {
        return $this->notImportant;
    }

    public function getKeywordNotContains(): string
    {
        return $this->keywordNotContains;
    }

    public function isUnread(): bool
    {
        return $this->unread;
    }

    public function getAsString(): string
    {
        if ($this->isAll()) {
            return 'ALL';
        }
        $flags = [];
        if ($this->isAnswered()) {
            $flags[] = 'ANSWERED';
        }
        if ($this->getBccContains() !== '') {
            $flags[] = 'BCC "' . $this->getBccContains() . '"';
        }
        if ($this->getBefore() !== null) {
            $flags[] = 'BEFORE "' . $this->formatDate($this->getBefore()) . '"';
        }
        if ($this->getBodyContains() !== '') {
            $flags[] = 'BODY "' . $this->getBodyContains() . '"';
        }
        if ($this->getCcContains() !== '') {
            $flags[] = 'CC "' . $this->getCcContains() . '"';
        }
        if ($this->isDeleted()) {
            $flags[] = 'DELETED';
        }
        if ($this->isImportant()) {
            $flags[] = 'FLAGGED';
        }
        if ($this->getFromContains() !== '') {
            $flags[] = 'FROM "' . $this->getFromContains() . '"';
        }
        if ($this->getKeywordContains() !== '') {
            $flags[] = 'KEYWORD "' . $this->getKeywordContains() . '"';
        }
        if ($this->getKeywordNotContains() !== '') {
            $flags[] = 'UNKEYWORD "' . $this->getKeywordNotContains() . '"';
        }
        if ($this->isNew()) {
            $flags[] = 'NEW';
        }
        if ($this->isOldOnly()) {
            $flags[] = 'OLD';
        }
        if ($this->getByDate() !== null) {
            $flags[] = 'ON "' . $this->formatDate($this->getByDate()) . '"';
        }
        if ($this->isRecent()) {
            $flags[] = 'RECENT';
        }
        if ($this->isRead()) {
            $flags[] = 'SEEN';
        }
        if ($this->isUnread()) {
            $flags[] = 'UNSEEN';
        }
        if ($this->getSince() !== null) {
            $flags[] = 'SINCE "' . $this->formatDate($this->getSince()) . '"';
        }
        if ($this->getSubjectContains() !== '') {
            $flags[] = 'SUBJECT "' . $this->getSubjectContains() . '"';
        }
        if ($this->getTextContains() !== '') {
            $flags[] = 'TEXT "' . $this->getTextContains() . '"';
        }
        if ($this->getToContains() !== '') {
            $flags[] = 'TO "' . $this->getToContains() . '"';
        }
        if ($this->isUnanswered()) {
            $flags[] = 'UNANSWERED';
        }
        if ($this->isNotDeleted()) {
            $flags[] = 'UNDELETED';
        }
        if ($this->isNotImportant()) {
            $flags[] = 'UNFLAGGED';
        }

        $flagsString = implode(' ', $flags);
        if ($flagsString !== '') {
            return $flagsString;
        }

        return 'ALL';
    }

    private function formatDate(\DateTimeInterface $dateTime): string
    {
        return $dateTime->format('j F Y');
    }

    public function __toString()
    {
        return $this->getAsString();
    }

}
