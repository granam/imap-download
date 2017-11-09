<?php
declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

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
    /** @var \DateTime|null */
    private $byDate;
    /** @var \DateTime|null */
    private $before;
    /** @var \DateTime|null */
    private $since;

    /**
     * @param string $charsetForSearch @link https://secure.php.net/manual/en/mbstring.supported-encodings.php
     */
    public function __construct(string $charsetForSearch = 'UTF-8')
    {
        $this->charsetForSearch = $charsetForSearch;
    }

    /** @return string */
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

    /**
     * @param \DateTime $messagesBefore
     * @return ImapSearchCriteria
     */
    public function setBefore(\DateTime $messagesBefore): ImapSearchCriteria // "date" - match messages with Date: before "date"
    {
        $this->before = $messagesBefore;

        return $this;
    }

    /**
     * @param string $stringInBody
     * @return ImapSearchCriteria
     */
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

    /**
     * @return ImapSearchCriteria
     */
    public function filterDeleted(): ImapSearchCriteria // - match deleted messages
    {
        $this->deleted = true;

        return $this;
    }

    /**
     * Filter only emails flagged as Important (sometimes Urgent)
     * @return $this
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

    public function filterByDate(\DateTime $dateTime): ImapSearchCriteria // "date" - match messages with Date: matching "date"
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

    public function filterSince(\DateTime $since): ImapSearchCriteria // "date" - match messages with Date: after "date"
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

    /**
     * @return bool
     */
    public function isAll(): bool
    {
        return $this->all;
    }

    /**
     * @return bool
     */
    public function isAnswered(): bool
    {
        return $this->answered;
    }

    /**
     * @return string
     */
    public function getBccContains(): string
    {
        return $this->bccContains;
    }

    /**
     * @return \DateTime|null
     */
    public function getBefore()
    {
        return $this->before;
    }

    /**
     * @return string
     */
    public function getBodyContains(): string
    {
        return $this->bodyContains;
    }

    /**
     * @return string
     */
    public function getCcContains(): string
    {
        return $this->ccContains;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @return bool
     */
    public function isImportant(): bool
    {
        return $this->important;
    }

    /**
     * @return string
     */
    public function getFromContains(): string
    {
        return $this->fromContains;
    }

    /**
     * @return string
     */
    public function getKeywordContains(): string
    {
        return $this->keywordContains;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->new;
    }

    /**
     * @return bool
     */
    public function isOldOnly(): bool
    {
        return $this->oldOnly;
    }

    /**
     * @return \DateTime|null
     */
    public function getByDate()
    {
        return $this->byDate;
    }

    /**
     * @return bool
     */
    public function isRecent(): bool
    {
        return $this->recent;
    }

    /**
     * @return bool
     */
    public function isRead(): bool
    {
        return $this->read;
    }

    /**
     * @return \DateTime|null
     */
    public function getSince()
    {
        return $this->since;
    }

    /**
     * @return string
     */
    public function getSubjectContains(): string
    {
        return $this->subjectContains;
    }

    /**
     * @return string
     */
    public function getTextContains(): string
    {
        return $this->textContains;
    }

    /**
     * @return string
     */
    public function getToContains(): string
    {
        return $this->toContains;
    }

    /**
     * @return bool
     */
    public function isUnanswered(): bool
    {
        return $this->unanswered;
    }

    /**
     * @return bool
     */
    public function isNotDeleted(): bool
    {
        return $this->notDeleted;
    }

    /**
     * @return bool
     */
    public function isNotImportant(): bool
    {
        return $this->notImportant;
    }

    /**
     * @return string
     */
    public function getKeywordNotContains(): string
    {
        return $this->keywordNotContains;
    }

    /**
     * @return bool
     */
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

    private function formatDate(\DateTime $dateTime): string
    {
        return $dateTime->format('j F Y');
    }

    public function __toString()
    {
        return $this->getAsString();
    }

}