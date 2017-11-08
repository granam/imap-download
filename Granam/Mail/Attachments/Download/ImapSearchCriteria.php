<?php
declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

namespace Granam\Mail\Attachments\Download;

use Granam\Strict\Object\StrictObject;

class ImapSearchCriteria extends StrictObject implements ToString
{
    /** @var string|null */
    private $charsetForSearch;
    /** @var bool */
    private $all = false;
    /** @var bool */
    private $answeredOnly = false;
    /** @var string|null */
    private $bccContains;
    /** @var \DateTime|null */
    private $before;
    /** @var string|null */
    private $bodyContains;
    /** @var string|null */
    private $ccContains;
    /** @var bool */
    private $deletedOnly = false;
    /** @var bool */
    private $flaggedOnly = false; // those marked as Important or Urgent
    /** @var string|null */
    private $fromContains;
    /** @var string|null */
    private $keywordContains;
    /** @var bool */
    private $newOnly = false;
    /** @var bool */
    private $oldOnly = false;
    /** @var \DateTime|null */
    private $byDate;
    /** @var bool */
    private $recentOnly = false;
    /** @var bool */
    private $readOnly = false;
    /** @var \DateTime|null */
    private $since;
    /** @var string|null */
    private $subjectContains;
    /** @var string|null */
    private $textContains;
    /** @var string|null */
    private $toContains;
    /** @var bool */
    private $unansweredOnly = false;
    /** @var bool */
    private $notDeletedOnly = false;
    /** @var bool */
    private $notFlaggedOnly = false;
    /** @var string|null */
    private $keywordNotContains;
    /** @var bool */
    private $notReadOnly = false;

    public function __construct(string $charsetForSearch = '')
    {
        $this->charsetForSearch = $charsetForSearch;
    }

    /** @return string */
    public function getCharsetForSearch(): string
    {
        return $this->charsetForSearch;
    }

    public function fetchAll(): ImapSearchCriteria // - return all messages matching the rest of the criteria
    {
        $this->all = true;

        return $this;
    }

    public function filterAnsweredOnly(): ImapSearchCriteria // - match messages with the \\ANSWERED flag set
    {
        $this->answeredOnly = true;

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
    public function filterDeletedOnly(): ImapSearchCriteria // - match deleted messages
    {
        $this->deletedOnly = true;

        return $this;
    }

    /**
     * Filter only emails flagged as Important (sometimes Urgent)
     * @return $this
     */
    public function filterFlaggedOnly(): ImapSearchCriteria // - match messages with the \\FLAGGED (sometimes referred to as Important or Urgent) flag set
    {
        $this->flaggedOnly = true;

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

    public function filterNewOnly(): ImapSearchCriteria // - match new messages
    {
        $this->newOnly = true;

        return $this;
    }

    public function filterOldOnly(): ImapSearchCriteria // - match old messages
    {
        $this->oldOnly = true;

        return $this;
    }

    public function filterByDate(\DateTime $dateTime): ImapSearchCriteria // "date" - match messages with Date: matching "date"
    {
        $this->byDate = $dateTime;

        return $this;
    }

    public function filterRecentOnly(): ImapSearchCriteria // - match messages with the \\RECENT flag set
    {
        $this->recentOnly = true;

        return $this;
    }

    public function filterReadOnly(): ImapSearchCriteria // - match messages that have been read (the \\SEEN flag is set)
    {
        $this->readOnly = true;

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

    public function filterUnansweredOnly(): ImapSearchCriteria // - match messages that have not been answered
    {
        $this->unansweredOnly = true;

        return $this;
    }

    public function filterNotDeletedOnly(): ImapSearchCriteria // - match messages that are not deleted
    {
        $this->notDeletedOnly = true;

        return $this;
    }

    public function filterNotFlaggedOnly(): ImapSearchCriteria // - match messages that are not flagged
    {
        $this->notFlaggedOnly = true;

        return $this;
    }

    public function filterKeywordNotContains(string $stringNotInKeyword): ImapSearchCriteria // "string" - match messages that do not have the keyword "string"
    {
        $this->keywordNotContains = $stringNotInKeyword;

        return $this;
    }

    public function filterNotReadOnly(): ImapSearchCriteria // - match messages which have not been read yet
    {
        $this->notReadOnly = true;

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
    public function isAnsweredOnly(): bool
    {
        return $this->answeredOnly;
    }

    /**
     * @return null|string
     */
    public function getBccContains()
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
     * @return null|string
     */
    public function getBodyContains()
    {
        return $this->bodyContains;
    }

    /**
     * @return null|string
     */
    public function getCcContains()
    {
        return $this->ccContains;
    }

    /**
     * @return bool
     */
    public function isDeletedOnly(): bool
    {
        return $this->deletedOnly;
    }

    /**
     * @return bool
     */
    public function isFlaggedOnly(): bool
    {
        return $this->flaggedOnly;
    }

    /**
     * @return null|string
     */
    public function getFromContains()
    {
        return $this->fromContains;
    }

    /**
     * @return null|string
     */
    public function getKeywordContains()
    {
        return $this->keywordContains;
    }

    /**
     * @return bool
     */
    public function isNewOnly(): bool
    {
        return $this->newOnly;
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
    public function isRecentOnly(): bool
    {
        return $this->recentOnly;
    }

    /**
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    /**
     * @return \DateTime|null
     */
    public function getSince()
    {
        return $this->since;
    }

    /**
     * @return null|string
     */
    public function getSubjectContains()
    {
        return $this->subjectContains;
    }

    /**
     * @return null|string
     */
    public function getTextContains()
    {
        return $this->textContains;
    }

    /**
     * @return null|string
     */
    public function getToContains()
    {
        return $this->toContains;
    }

    /**
     * @return bool
     */
    public function isUnansweredOnly(): bool
    {
        return $this->unansweredOnly;
    }

    /**
     * @return bool
     */
    public function isNotDeletedOnly(): bool
    {
        return $this->notDeletedOnly;
    }

    /**
     * @return bool
     */
    public function isNotFlaggedOnly(): bool
    {
        return $this->notFlaggedOnly;
    }

    /**
     * @return null|string
     */
    public function getKeywordNotContains()
    {
        return $this->keywordNotContains;
    }

    /**
     * @return bool
     */
    public function isNotReadOnly(): bool
    {
        return $this->notReadOnly;
    }

    public function getAsString(): string
    {
        if ($this->isAll()) {
            return 'ALL';
        }
        $flags = [];
        if ($this->isAnsweredOnly()) {
            $flags[] = 'ANSWERED';
        }
        if ($this->getBccContains() !== null) {
            $flags[] = 'BCC "' . $this->getBccContains() . '"';
        }
        if ($this->getBefore() !== null) {
            $flags[] = 'BEFORE "' . $this->formatDate($this->getBefore()) . '"';
        }
        if ($this->getBodyContains() !== null) {
            $flags[] = 'BODY "' . $this->getBodyContains() . '"';
        }
        if ($this->getCcContains() !== null) {
            $flags[] = 'CC "' . $this->getCcContains() . '"';
        }
        if ($this->isDeletedOnly()) {
            $flags[] = 'DELETED';
        }
        if ($this->isFlaggedOnly()) {
            $flags[] = 'FLAGGED';
        }
        if ($this->getFromContains() !== null) {
            $flags[] = 'FROM "' . $this->getFromContains() . '"';
        }
        if ($this->getKeywordContains() !== null) {
            $flags[] = 'KEYWORD "' . $this->getKeywordContains() . '"';
        }
        if ($this->getKeywordNotContains() !== null) {
            $flags[] = 'UNKEYWORD "' . $this->getKeywordNotContains() . '"';
        }
        if ($this->isNewOnly()) {
            $flags[] = 'NEW';
        }
        if ($this->isOldOnly()) {
            $flags[] = 'OLD';
        }
        if ($this->getByDate() !== null) {
            $flags[] = 'ON "' . $this->formatDate($this->getByDate()) . '"';
        }
        if ($this->isRecentOnly()) {
            $flags[] = 'RECENT';
        }
        if ($this->isReadOnly()) {
            $flags[] = 'SEEN';
        }
        if ($this->getSince() !== null) {
            $flags[] = 'SINCE "' . $this->formatDate($this->getSince()) . '"';
        }
        if ($this->getSubjectContains() !== null) {
            $flags[] = 'SUBJECT "' . $this->getSubjectContains() . '"';
        }
        if ($this->getTextContains() !== null) {
            $flags[] = 'TEXT "' . $this->getTextContains() . '"';
        }
        if ($this->getToContains() !== null) {
            $flags[] = 'TO "' . $this->getToContains() . '"';
        }
        if ($this->isUnansweredOnly()) {
            $flags[] = 'UNANSWERED';
        }
        if ($this->isNotDeletedOnly()) {
            $flags[] = 'UNDELETED';
        }
        if ($this->isNotFlaggedOnly()) {
            $flags[] = 'UNFLAGGED';
        }
        if ($this->isNotReadOnly()) {
            $flags[] = 'UNSEEN';
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