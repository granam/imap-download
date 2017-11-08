<?php
namespace Granam\Mail\Attachments\Download;

use PHPUnit\Framework\TestCase;

class ImapSearchCriteriaTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_create_it_with_any_search_charset()
    {
        $imapSearchCriteria = new ImapSearchCriteria();
        self::assertSame('', $imapSearchCriteria->getCharsetForSearch());
        $imapSearchCriteria = new ImapSearchCriteria('UTF-8');
        self::assertSame('UTF-8', $imapSearchCriteria->getCharsetForSearch());
    }

    /**
     * @test
     */
    public function I_can_chain_setters()
    {
        $reflectionClass = new \ReflectionClass(ImapSearchCriteria::class);
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (strpos($method->getShortName(), 'is') === 0 || strpos($method->getShortName(), 'get') === 0 || strpos($method->getShortName(), '__') === 0) {
                continue;
            }
            self::assertTrue($method->hasReturnType(), "Method {$method->getName()} should have a return type");
            $returnType = $method->getReturnType();
            /** @noinspection NullPointerExceptionInspection */
            self::assertSame(ImapSearchCriteria::class, $returnType->getName());
        }
    }

    /**
     * @test
     */
    public function I_will_get_all_as_filter_when_nothing_set()
    {
        $imapSearchCriteria = new ImapSearchCriteria();
        self::assertSame('ALL', $imapSearchCriteria->getAsString());
        self::assertSame('ALL', (string)$imapSearchCriteria);
        $imapSearchCriteria->fetchAll();
        self::assertSame('ALL', $imapSearchCriteria->getAsString());
        self::assertSame('ALL', (string)$imapSearchCriteria);
    }

    /**
     * @test
     */
    public function I_can_get_filter_every_possible_combination()
    {
        $imapSearchCriteria = new ImapSearchCriteria();
        $imapSearchCriteria->filterAnsweredOnly();
        self::assertTrue($imapSearchCriteria->isAnsweredOnly());
        self::assertSame('ANSWERED', $imapSearchCriteria->getAsString());
        $imapSearchCriteria->filterByDate($now = new \DateTime());
        self::assertSame($now, $imapSearchCriteria->getByDate());
        self::assertSame('ANSWERED ON "' . $now->format('j F Y') . '"', $imapSearchCriteria->getAsString());
        $imapSearchCriteria->filterDeletedOnly();
        self::assertTrue($imapSearchCriteria->isDeletedOnly());
        self::assertSame('ANSWERED DELETED ON "' . $now->format('j F Y') . '"', $imapSearchCriteria->getAsString());
        $imapSearchCriteria->filterFlaggedOnly();
        self::assertTrue($imapSearchCriteria->isFlaggedOnly());
        self::assertSame('ANSWERED DELETED FLAGGED ON "' . $now->format('j F Y') . '"', $imapSearchCriteria->getAsString());
        $imapSearchCriteria->filterKeywordContains('foo');
        self::assertSame('foo', $imapSearchCriteria->getKeywordContains());
        self::assertSame('ANSWERED DELETED FLAGGED KEYWORD "foo" ON "' . $now->format('j F Y') . '"', $imapSearchCriteria->getAsString());
        $imapSearchCriteria->filterKeywordNotContains('bar');
        self::assertSame('bar', $imapSearchCriteria->getKeywordNotContains());
        self::assertSame('ANSWERED DELETED FLAGGED KEYWORD "foo" UNKEYWORD "bar" ON "' . $now->format('j F Y') . '"', $imapSearchCriteria->getAsString());
        $imapSearchCriteria->filterNewOnly();
        self::assertTrue($imapSearchCriteria->isNewOnly());
        self::assertSame('ANSWERED DELETED FLAGGED KEYWORD "foo" UNKEYWORD "bar" NEW ON "' . $now->format('j F Y') . '"', $imapSearchCriteria->getAsString());
    }
}
